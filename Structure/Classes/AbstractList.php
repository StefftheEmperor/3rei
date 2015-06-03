<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 18:50
 */

namespace Structure\Classes;


abstract class AbstractList extends \Structure\Classes\AbstractStructure {

	protected $elements = array();

	public function get_elements()
	{
		return $this->elements;
	}

	public function add(\Structure\Classes\AbstractStructure $element)
	{
		$this->elements[] = $element;

		return $this;
	}
}