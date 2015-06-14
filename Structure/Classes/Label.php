<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 17:25
 */

namespace Structure\Classes;


class Label extends \Structure\Classes\AbstractValueStructure
{

	public function get_html()
	{
		return '<label'.$this->get_attributes_html().'>'.$this->get_name().'</label>';
	}
}