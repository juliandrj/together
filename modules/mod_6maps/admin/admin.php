<?php
/**
* @package   6maps
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.form.formfield');
$document = JFactory::getDocument();
$document->addStylesheet(JURI::base(true) . '/../modules/mod_6maps/admin/css/style.css');
$document->addScript(JURI::root() . "modules/mod_6maps/admin/js/admin.js");

// The class name must always be the same as the filename (in camel case)
class JFormFieldadmin extends JFormField
{
	//The field class must know its own type through the variable $type.
	protected $type = 'admin';
	public function getInput() {}
}
?>