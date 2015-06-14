<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 17:23
 */

namespace Structure\Classes;


use Request\Classes\Request\Post;
use Structure\Interfaces\Value;

class KeyValuePair extends AbstractStructure
implements Value
{

	protected $label = NULL;
	protected $value = NULL;

	/**
	 * @param AbstractStructure $label
	 * @param AbstractStructure $value
	 */
	public function init(AbstractStructure $label, AbstractStructure $value)
	{
		$this->label = $label;
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function get_html()
	{
		return '<dl><dt>'.$this->get_renderer()->render($this->label).'</dt><dd>'.$this->get_renderer()->render($this->value).'</dd></dl>';
	}

	/**
	 * @param Post $post_data
	 * @return mixed
	 */
	public function validate(Post $post_data)
	{
		return $this->value->validate($post_data);
	}

	/**
	 * @return mixed
	 */
	public function get_value()
	{
		return $this->value->get_value();
	}

	/**
	 * @param $value
	 * @return $this
	 */
	public function set_value($value)
	{
		$this->value->set_value($value);

		return $this;
	}

	/**
	 * @param $field
	 * @return mixed
	 */
	public function get_value_of($field)
	{
		return $this->value->get_value_of($field);
	}

	/**
	 * @return mixed
	 */
	public function get_key()
	{
		return $this->value->get_key();
	}

	public function get_value_object()
	{
		return $this->value;
	}
	/**
	 * @param $key
	 * @return $this
	 */
	public function set_key($key)
	{
		$this->value->set_key($key);

		return $this;
	}
}