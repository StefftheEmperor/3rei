<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 25.01.15
 * Time: 18:31
 */

namespace Request\Classes;


class Controller {

	/**
	 * @var \Request\Classes\Request $request
	 */
	protected $request = NULL;

	protected $layout = NULL;

	protected $view = NULL;
	/**
	 * @param Request $request
	 */
	public final function __construct(\Request\Classes\Request $request)
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

	public function set_layout($layout)
	{
		$this->layout = $layout;

		return $this;
	}

	public function get_registry()
	{
		return \Model\Classes\Registry::get_instance();
	}

	public function get_database_connection()
	{
		return $this->get_request()->get_model()->get_connection();
	}

	public function get_view()
	{
		if ( ! isset($this->view)) {

			$request = $this->get_request();

			if ($request->param_exists('view')) {
				$view_name = $request->get_param('view');
				$view_class_name = '\\' . $request->get_param('module') . '\\View\\' . $request->get_param('controller') . '\\' . $view_name;
			} else {
				$view_class_name = '\\' . $request->get_param('module') . '\\View\\' . $request->get_param('controller') . '\\' . $request->get_param('action');
			}

			if ( ! class_exists($view_class_name))
			{
				throw new \Request\Classes\View\Exception('View not found: '.$view_class_name);
			}
			$view_class_reflector = new \ReflectionClass($view_class_name);

			$this->view = $view_class_reflector->newInstance();
		}

		return $this->view;
	}

	public function set_view($view)
	{
		$this->get_request()->set_param('view', $view);

		return $this;
	}
}