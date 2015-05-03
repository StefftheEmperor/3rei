<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 25.01.15
 * Time: 18:31
 */

namespace Request\Classes;


class Controller {

	protected $request = NULL;

	protected $layout = NULL;
	public final function __construct($request)
	{
		$this->request = $request;
	}

	public function get_request()
	{
		return $this->request;
	}

	public function init()
	{

	}

	public function before_action()
	{

	}

	public function after_action()
	{

	}

	public function get_layout()
	{
		return $this->layout;
	}

	public function get_view()
	{
		$request =  $this->get_request();

		if ($request->param_exists('view'))
		{
			$view_name = $request->get_param('view');
			$view_class_name = '\\'.$request->get_param('module').'\\View\\'.$view_name;
		} else {
			$view_class_name =  '\\'.$request->get_param('module').'\\View\\'.$request->get_param('controller').'\\'.$request->get_param('action');
		}

		$view_class_reflector = new \ReflectionClass($view_class_name);

		return $view_class_reflector->newInstance();
	}
}