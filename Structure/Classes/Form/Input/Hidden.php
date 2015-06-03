<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 17:26
 */

namespace Structure\Classes\Form\Input;


class Hidden extends \Structure\Classes\Form\AbstractValue {

	public function get_html()
	{
		$this->get_attributes()->type = 'hidden';
		$this->get_attributes()->value = $this->get_value();

		return '<input'.$this->get_attributes_html().' />';
	}
}