<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 25.01.15
 * Time: 21:47
 */

namespace Request\Classes;


class View extends \Model\Classes\AbstractModel implements \Request\Interfaces\Renderable {

	protected $renderer = NULL;

	protected $controller = NULL;

	public function set_controller(\Request\Classes\Controller $controller)
	{
		$this->controller = $controller;

		return $this;
	}

	/**
	 * @return \Request\Classes\Controller
	 */
	public function get_controller()
	{
		return $this->controller;
	}

	public function get_renderer()
	{
		return $this->renderer;
	}

	public function set_renderer($renderer)
	{
		$this->renderer = $renderer;
		return $this;
	}

	public function __get($key)
	{
		$value = parent::__get($key);

		if ($value instanceof \Request\Interfaces\Renderable)
		{
			$value->set_renderer($this->get_renderer());
			$value = $this->get_renderer()->render($value);
		}

		return $value;
	}
}