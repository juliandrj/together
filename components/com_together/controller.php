<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class TogetherController extends JControllerLegacy {
	
	const MASCULINO = 'M';
	const FEMENINO = 'F';
	
	const CADERA = 1;
	const CINTURA = 2;
	const TALLA = 3;
	
	const PROTREINAS = 1;
	const CARBOHIDRATOS = 2;
	const GRASAS = 3;
	
	const GR_PROTEINAS = 7;
	const GR_CARBOHIDRATOS = 9;
	const GR_GRASAS = 12;
	
	const NUMERO_COMIDAS = 5;
	const POR_COMIDAS_PPALES = 0.275;
	const POR_COMIDAS_INTERMEDIA = 0.0875;
	
	const MPESO = 10;
	const MALTURA = 9;
	const MCINTURA = 8;
	const MCADMUN = 7;
	
	private $nombreComidas = ["Desayuno", "Mediasnueves", "Almuerzo", "Onces", "Cena"];
	
	public function calcularIMC($documento = null) {
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');
		try {
			if (!$documento) {
				$documento = JFactory::getApplication()->input->getString("documento");
			}
			$m = $this->getModel();
			$persona = $m->buscarPersona($documento);
			if (!$persona) {
				throw new Exception(JText::_('PERSON_NOTFOUND'));
			}
			$data = new stdClass();
			$data->genero = $persona->genero;
			$data->peso = $m->getMedida($persona->uid, self::MPESO);
			$data->altura = $m->getMedida($persona->uid, self::MALTURA);
			$data->cintura = $m->getMedida($persona->uid, self::MCINTURA);
			$data->cadmun = $m->getMedida($persona->uid, self::MCADMUN);
			$data->afisica = $m->getAFisica($persona->afisica);
			$imc = $this->imc($data);
			$imc->dieta = $this->generarDieta($persona->uid, $imc);
			$imc->rutina = $m->getRutina($imc->grasa);
			echo json_encode($imc);
		} catch (Exception $ex) {
			echo '{"exception":"' . $ex->getMessage() . '"}';
		}
		JFactory::getApplication()->close();
	}
	
	private function imc($input) {
		$valores = null;
		switch ($input->genero) {
			case self::FEMENINO:
				$valores = $this->imcMujeres($input);
				break;
			case self::MASCULINO:
				$valores = $this->imcHombres($input);
				break;
		}
		return $valores;
	}
	
	private function imcMujeres ($in) {
		$peso = floatval($in->peso);
		$altura = floatval($in->altura);
		$cadera = $this->getModel()->getConstant(self::FEMENINO, self::CADERA, floatval($in->cadmun));
		$cintura = $this->getModel()->getConstant(self::FEMENINO, self::CINTURA, floatval($in->cintura));
		$talla = $this->getModel()->getConstant(self::FEMENINO, self::TALLA, $altura);
		$constantes = new stdClass();
		$constantes->grasa = round(($cadera + $cintura) - $talla, 0);
		$constantes->imc = round($peso / pow($altura / 100, 2), 0);
		$constantes->magro = round($peso - ($peso * $constantes->grasa / 100), 0);
		$constantes->proteina = ceil($constantes->magro * floatval($in->afisica));
		return $constantes;
	}
	
	private function imcHombres ($in) {
		$peso = floatval($in->peso);
		$altura = floatval($in->altura);
		$constantes = new stdClass();
		$constantes->grasa = round($this->getModel()->getConstant(self::MASCULINO, $peso, floatval($in->cintura) - floatval($in->cadmun)), 4);
		$constantes->imc = round($peso / pow($altura / 100, 2), 0);
		$constantes->magro = round($peso - ($peso * $constantes->grasa / 100), 4);
		$constantes->proteina = ceil($constantes->magro * floatval($in->afisica));
		return $constantes;
	}
	
	private function generarDieta($uid, $valores) {
		try {
			$proteinas = $this->getModel()->getAlimentos($uid, self::PROTREINAS, self::NUMERO_COMIDAS);
			$carbohidratos = $this->getModel()->getAlimentos($uid, self::CARBOHIDRATOS, self::NUMERO_COMIDAS);
			$grasas = $this->getModel()->getAlimentos($uid, self::GRASAS, self::NUMERO_COMIDAS);
			$dieta = [];
			for ($i = 0; $i < self::NUMERO_COMIDAS; $i ++) {
				$comida = new stdClass();
				$comida->nombre = $this->nombreComidas[$i];
				$k = ($valores->proteina * ($i % 2 == 0 ? self::POR_COMIDAS_PPALES : self::POR_COMIDAS_INTERMEDIA)) / self::GR_PROTEINAS;
				$comida->k = round($k, 1);
				$comida->proteina = $proteinas[$i];
				$comida->proteina->valor = round($comida->proteina->valor * $k, 0);
				$comida->carbohidratos = $carbohidratos[$i];
				$comida->carbohidratos->valor = round($comida->carbohidratos->valor * $k, 0);
				$comida->grasas = $grasas[$i];
				$comida->grasas->valor = round($comida->grasas->valor * $k, 0);
				$dieta[] = $comida;
			}
			return $dieta;
		} catch (Exception $ex) {
			return '{"exception":"' . $ex->getMessage() . '"}';
		}
	}
	
	public function buscar() {
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');
		try {
			$in = JFactory::getApplication()->input;
			$persona = $this->getModel()->buscarPersona($in->getString("documento"));
			if (!$persona) {
				throw new Exception(JText::_('PERSON_NOTFOUND'));
			}
			$persona->fecha_nacimiento = date('d/m/Y', $persona->fecha_nacimiento);
			$user = JFactory::getUser($persona->uid);
			$persona->nombres = $user->name;
			$persona->email = $user->email;
			$antecedentes = $this->getModel()->buscarAntecedentes($user->id);
			$medidas = $this->getModel()->buscarMedidas($user->id);
			$res = new stdClass();
			$res->persona = $persona;
			$res->antecedentes = $antecedentes;
			$res->medidas = $medidas;
			echo json_encode($res);
		} catch (Exception $e) {
			echo "{\"exception\":\"" . $e->getMessage() . "\"}";
		}
		JFactory::getApplication()->close();
	}
	
	public function registrar() {
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');
		try {
			$in = JFactory::getApplication()->input;
			$data = array();
			$data['name'] = $in->getString('nombre');
			$data['username'] = $in->getString('email');
			$data['email'] = $in->getString('email');
			$data['tipo_documento'] = $in->getString('tipo_documento');
			$data['documento'] = $in->getString('documento');
			$data['genero'] = $in->getString('genero');
			$data['nacimiento'] = $in->getString('nacimiento');
			$data['telefono'] = $in->getString('telefono');
			$data['profesion'] = $in->getString('profesion');
			$data['afisica'] = $in->getString('afisica');
			$persona = $this->getModel()->buscarPersona($data['documento']);
			$user = $persona ? JFactory::getUser($persona->uid) : $this->getModel()->registrarUsuarioJoomla($data);
			$data['uid'] = $user->id;
			$this->getModel()->registrarPersona($data, $persona);
			$ants = array();
			$i = 0;
			while ($in->get('antecedente-'.$i, null) != null) {
				if ($in->getInt('antecedente-'.$i) == 0) {
					$i ++;
					continue;
				}
				$ant = array();
				$ant['uid'] = $user->id;
				$ant['antecedente'] = $in->getInt('antecedente-'.$i);
				$ant['cuales'] = $in->get('cuales-'.$i, null) != null ? $in->get('cuales-'.$i) : '';
				$ants[] = $ant;
				$i ++;
			}
			$this->getModel()->registrarAntecedentes($ants);
			$meds = array();
			$i = 0;
			while ($in->get('medida-'.$i, null) != null) {
				$med = array();
				$med['uid'] = $user->id;
				$med['medida'] = $in->getString('medida-'.$i);
				$med['valor'] = $in->getFloat('valor-'.$i);
				$meds[] = $med;
				$i ++;
			}
			$this->getModel()->registrarMedidas($meds);
			echo "{\"status\":\"OK\"}";
		} catch (Exception $e) {
			echo "{\"exception\":\"" . $e->getMessage() . "\"}";
		}
		JFactory::getApplication()->close();
	}
	
}