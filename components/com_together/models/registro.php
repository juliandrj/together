<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');

class TogetherModelRegistro extends JModelItem {

	protected $listadoAntecedentes = null;
	protected $listadoMedidas = null;

	public function getListadoAntecedentes() {
		try {
			if ($this->listadoAntecedentes) {
				return $this->listadoAntecedentes;
			}
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__together_antecedente');
			$query->where("activo = 1");
			$query->order("cuales, antecedente");
			$db->setQuery((string)$query);
			$this->listadoAntecedentes = $db->loadObjectList();
			return $this->listadoAntecedentes;
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return -1;
		}
	}

	public function getListadoMedidas() {
		try {
			if ($this->listadoMedidas) {
				return $this->listadoMedidas;
			}
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__together_medida');
			$query->where("activo = 1");
			$query->order("medida");
			$db->setQuery((string)$query);
			$this->listadoMedidas = $db->loadObjectList();
			return $this->listadoMedidas;
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return -1;
		}
	}

}