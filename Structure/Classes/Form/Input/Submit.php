<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 19:44
 */

namespace Structure\Classes\Form\Input;


class Submit extends \Structure\Classes\Form\AbstractValue {
	public function get_html()
	{
		$this->get_attributes()->type = 'submit';
		$this->get_attributes()->value = $this->get_value();

		return '<input'.$this->get_attributes_html().' />';
	}
}