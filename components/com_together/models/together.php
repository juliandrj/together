<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
 
class TogetherModelTogether extends JModelItem {

	/**
	 * Retorna la constante segun el caso.
	 * @param string $genero
	 * @param integer $x
	 * @param integer $y
	 * @return double
	 */
	public function getConstant($genero, $x, $y) {
		try {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('constante');
			$query->from('#__imc_constants');
			$query->where("genero='".$genero."'")->where("x=".$x)->where("y=".$y);
			$db->setQuery((string)$query);
			$val = $db->loadObject();
			return $val->constante;
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return -1;
		}
	}
	
}