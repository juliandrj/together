<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class TogetherController extends JControllerLegacy {
	
	const MASCULINO = 'M';
	const FEMENINO = 'F';
	
	const CADERA = 1;
	const CINTURA = 2;
	const TALLA = 3;
	
	public function calcularIMC() {
		try {
			JFactory::getDocument()->setMimeEncoding( 'application/json' );
			JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');
			$input = JFactory::getApplication()->input;
			switch ($input->get('genero')) {
				case self::FEMENINO:
					echo json_encode($this->imcMujeres($input));
					break;
				case self::MASCULINO:
					echo json_encode($this->imcHombres($input));
					break;
				default:
					echo '{"exception":"No se pueden calcular los valores, genero no valido: ' . $input->get('genero') . '."}';
			}
			JFactory::getApplication()->close(); // or jexit();
		} catch (Exception $ex) {
			echo '{"exception":"' . $ex->getMessage() . '"}';
		}
	}
	
	private function imcMujeres ($in) {
		$peso = floatval($in->get('peso'));
		$altura = intval($in->get('talla'));
		$cadera = $this->getModel()->getConstant(self::FEMENINO, self::CADERA, intval($in->get('cadera')));
		$cintura = $this->getModel()->getConstant(self::FEMENINO, self::CINTURA, intval($in->get('cinmun')));
		$talla = $this->getModel()->getConstant(self::FEMENINO, self::TALLA, $altura);
		$constantes = new stdClass();
		$constantes->grasa = round(($cadera + $cintura) - $talla, 4);
		//$constantes->imc = round($peso / pow($altura, 2), 4);
		$constantes->imc = round($peso - ($peso * $constantes->grasa / 100), 4);
		return $constantes;
	}
	
	private function imcHombres ($in) {
		$peso = floatval($in->get('peso'));
		$constantes = new stdClass();
		$constantes->grasa = round($this->getModel()->getConstant(self::MASCULINO, $peso, intval($in->get('cadera')) - intval($in->get('cinmun'))), 4);
		$constantes->imc = round($peso - ($peso * $constantes->grasa / 100), 4);
		return $constantes;
	}
	
}