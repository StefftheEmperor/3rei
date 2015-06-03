<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 16:58
 */

namespace Structure\Classes\Table\Row;


class Cell extends \Structure\Classes\AbstractStructure {

	protected $value;

	public function init($value)
	{
		$this->value = $value;
	}

	public function get_value()
	{
		return $this->value;
	}

	public function get_html()
	{
		$cell = '<td>'.$this->get_renderer()->render($this->get_value()).'</td>';
		return $cell;
	}
}