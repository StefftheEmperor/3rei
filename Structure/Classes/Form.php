<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 17:22
 */

namespace Structure\Classes;
use Request\Classes\Request\Post;
use Structure\Interfaces\Value;

/**
 * Class Form
 * @package Structure\Classes
 */
class Form extends \Structure\Classes\AbstractStructure
{

	const METHOD_POST = 'post';
	const METHOD_GET = 'get';

	/**
	 * @var array
	 */
	protected $fields = array();

	/**
	 * @var null
	 */
	protected $identifier = NULL;

	/**
	 * @param AbstractStructure $structure
	 * @return $this
	 */
	public function add(AbstractStructure $structure)
	{
		$this->fields[] = $structure;

		return $this;
	}

	/**
	 * @return array
	 */
	public function get_fields()
	{
		return $this->fields;
	}

	/**
	 * @return string
	 */
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

	/**
	 * @param $identifier
	 */
	public function init($identifier)
	{
		$this->identifier = $identifier;
		$this->add(\Structure\Classes\Form\Input\Hidden::factory($this->identifier.'_ds','1'));
	}

	/**
	 * @param $post_data
	 * @return bool
	 */
	public function is_submitted($post_data)
	{
		return isset($post_data[$this->identifier.'_ds']);
	}

	/**
	 * @param Post $post
	 * @return $this
	 */
	public function validate(Post $post)
	{
		if ($this->is_submitted($post)) {
			foreach ($this->get_fields() as $field) {
				if ($field instanceof Value) {
					$field->validate($post);
				}
			}
		}

		return $this;
	}

	public function get_value_of($field_name)
	{
		$value = NULL;
		foreach ($this->get_fields() as $field) {
			if ($field instanceof Value)
			{
				$value = $field->get_value_of($field_name);
				if (isset($value))
				{
					break;
				}
			}
		}

		return $value;
	}
	/**
	 * @return string
	 */
	public function get_html()
	{
		return '<form'.$this->get_attributes_html().'>'.$this->get_fields_html().'</form>';
	}
}