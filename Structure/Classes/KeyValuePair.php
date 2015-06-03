<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 17:23
 */

namespace Structure\Classes;


class KeyValuePair extends \Structure\Classes\AbstractStructure
{

	protected $label = NULL;
	protected $value = NULL;

	public function init(\Structure\Classes\AbstractStructure $label, \Structure\Classes\AbstractStructure $value)
	{
		$this->label = $label;
		$this->value = $value;
	}

	public function get_html()
	{
		return '<dl><dt>'.$this->get_renderer()->render($this->label).'</dt><dd>'.$this->get_renderer()->render($this->value).'</dd></dl>';
	}
}