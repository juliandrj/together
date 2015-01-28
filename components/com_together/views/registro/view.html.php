<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class TogetherViewRegistro extends JViewLegacy {

	function display($tpl = null) {
		$this->antecedentes = $this->get('ListadoAntecedentes');
		$this->medidas = $this->get('ListadoMedidas');
		parent::display($tpl);
		
	}

}