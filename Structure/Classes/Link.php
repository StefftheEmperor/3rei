<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 13:20
 */

namespace Structure\Classes;


class Link extends \Structure\Classes\AbstractValueStructure {



	public function get_html()
	{
		$value = $this->get_value();
		return '<a'.$this->get_attributes_html().'>'.(isset($value) ? $this->get_renderer()->render($value) : '').'</a>';
	}
}