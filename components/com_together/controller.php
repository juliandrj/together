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
	
	private $nombreComidas = ["Desayuno", "Mediasnueves", "Almuerzo", "Onces", "Cena"];
	
	public function calcularIMC() {
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');
		try {
			$imc = $this->imc(JFactory::getApplication()->input);
			$imc->dieta = $this->generarDieta(0, $imc);
			echo json_encode($imc);
		} catch (Exception $ex) {
			echo '{"exception":"' . $ex->getMessage() . '"}';
		} finally {
			JFactory::getApplication()->close();
		}
	}
	
	private function imc($input) {
		$valores = null;
		switch ($input->get('genero')) {
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
		$peso = floatval($in->get('peso'));
		$altura = intval($in->get('talla'));
		$cadera = $this->getModel()->getConstant(self::FEMENINO, self::CADERA, intval($in->get('cadmun')));
		$cintura = $this->getModel()->getConstant(self::FEMENINO, self::CINTURA, intval($in->get('cintura')));
		$talla = $this->getModel()->getConstant(self::FEMENINO, self::TALLA, $altura);
		$constantes = new stdClass();
		$constantes->grasa = round(($cadera + $cintura) - $talla, 0);
		$constantes->imc = round($peso / pow($altura / 100, 2), 0);
		$constantes->magro = round($peso - ($peso * $constantes->grasa / 100), 0);
		$constantes->proteina = ceil($constantes->magro * floatval($in->get('actividad')));
		return $constantes;
	}
	
	private function imcHombres ($in) {
		$peso = floatval($in->get('peso'));
		$altura = intval($in->get('talla'));
		$constantes = new stdClass();
		$constantes->grasa = round($this->getModel()->getConstant(self::MASCULINO, $peso, intval($in->get('cintura')) - intval($in->get('cadmun'))), 4);
		$constantes->imc = round($peso - ($peso * $constantes->grasa / 100), 4);
		$constantes->magro = round($peso - ($peso * $constantes->grasa / 100), 4);
		$constantes->proteina = round($constantes->magro * floatval($in->get('actividad')), 4);
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
	
}