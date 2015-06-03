<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 17:22
 */

namespace Structure\Classes;


class Form extends \Structure\Classes\AbstractStructure
{
	const METHOD_POST = 'post';
	const METHOD_GET = 'get';

	protected $fields = array();

	protected $identifier = NULL;
	public function add(\Structure\Classes\AbstractStructure $structure)
	{
		$this->fields[] = $structure;

		return $this;
	}

	public function get_fields()
	{
		return $this->fields;
	}

	public function get_fields_html()
	{
		$fields_html = '';
		foreach ($this->get_fields() as $field)
		{
			$field->set_renderer($this->get_renderer());
			$fields_html .= $field->get_renderer()->render($field);
		}

		return $fields_html;
	}

	public function init($identifier)
	{
		$this->identifier = $identifier;
		$this->add(\Structure\Classes\Form\Input\Hidden::factory($this->identifier.'_ds','1'));
	}

	public function is_submitted($post_data)
	{
		return isset($post_data[$this->identifier.'_ds']);
	}
	public function get_html()
	{
		return '<form'.$this->get_attributes_html().'>'.$this->get_fields_html().'</form>';
	}
}