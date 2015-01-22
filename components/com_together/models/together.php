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
			return isset($val->constante) ? $val->constante : 0;
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return -1;
		}
	}
	
	public function getAlimentos($uid, $tipo, $limit) {
		try {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('st.subtipo')->select('al.nombre')->select('al.valor as valor')->select('al.unidad');
			$query->from('#__together_alimentos al')->from('#__together_subtipo st');
			$query->where("al.subtipo = st.id")->where("st.tipo=" . $tipo);
			$query->order("rand() limit " . $limit);
			$db->setQuery((string)$query);
			return $db->loadObjectList();
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return -1;
		}
	}
	
}