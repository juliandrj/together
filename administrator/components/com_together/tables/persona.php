<?php defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class TogetherTablePersona extends JTable {
	
	public function __construct(&$db) {
		parent::__construct('#__together_persona', 'uid', $db);
	}
	
	public function bind($array, $ignore = '') {
		return parent::bind($array, $ignore);
	}
	
	public function check() {
		return parent::check();
	}
	
	public function store($updateNulls = false) {
		return parent::store($updateNulls);
	}
	
}