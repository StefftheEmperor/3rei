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
	public final function __construct($request)
	{
		$this->request = $request;
	}

	public function get_request()
	{
		return $this->request;
	}

	public function get_view()
	{
		$request =  $this->get_request();

		if (isset($request['view']))
		{
			$view_name = $request['view'];
			$view_class_name = '\\'.$request->get_module().'\\View\\'.$view_name;
		} else {
			$view_class_name =  '\\'.$request->get_module().'\\View\\'.$request->get_controller().'\\'.$request->get_action();
		}

		$view_class_reflector = new \ReflectionClass($view_class_name);

		return $view_class_reflector->newInstance();
	}
}