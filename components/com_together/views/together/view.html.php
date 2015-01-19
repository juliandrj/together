<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class TogetherViewTogether extends JViewLegacy {

	function display($tpl = null) {
		$this->msg = "Together!";
		parent::display($tpl);
	}

}