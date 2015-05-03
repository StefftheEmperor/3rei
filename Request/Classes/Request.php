<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 16.11.14
 * Time: 17:27
 */
namespace Request\Classes;
class Request extends \Model\Classes\AbstractModel {

	protected $url = NULL;

	protected $model = NULL;

	protected $params = NULL;
	public function __construct($url = NULL)
	{
		if (isset($url)) {
			$this->set_url($url);
		}
	}

	/**
	 * @param Url $url
	 * @return \Request\Classes\Request
	 */
	public static function factory_by_url(\Request\Classes\Url $url)
	{
		$request = new static;
		$request->set_url($url);

		$url->get_rewrite()->set_request($request);
		return $request;
	}

	public static function factory_by_id($id)
	{
		$request = new static;
		$model = \Request\Model\Request::factory_by_id($id);

		$request->set_model($model);

		return $request;
	}

	public static function factory_by_model(\Request\Model\Request $request_model)
	{
		$request = new static;
		$request->set_model($request_model);

		return $request;
	}
	public function set_url(\Request\Classes\Url $url)
	{
		$this->url = $url;

		return $this;
	}

	public function get_url()
	{
		return $this->url;
	}

	public function set_model(\Request\Model\Request $request_model)
	{
		$this->model = $request_model;
	}

	public function get_model()
	{
		return $this->model;
	}

	public function save_rewrite()
	{

		$this->get_url()->get_rewrite()->save();

		return $this;
	}

	public function get_params()
	{
		if ( ! isset($this->params))
		{
			$this->load_params();
		}

		return $this->params;
	}

	public function load_params()
	{
		$this->get_model()->load_params();

		return $this;
	}

	public function get_param($key)
	{
		return $this->get_model()->get_param($key);
	}

	public function set_param($key, $value)
	{
		$this->get_model()->set_param($key, $value);
	}
	public function param_exists($key)
	{
		return $this->get_model()->param_exists($key);
	}
	/**
	 * @return mixed
	 */
	public function execute()
	{

		if ( ! ($this->param_exists('module') AND $this->param_exists('controller') AND $this->param_exists('action')))
		{
			$this->set_param('module', 'Error');
			$this->set_param('controller', 'Error');
			$this->set_param('action', 'Error_404');
		}

		if ( ! $this->param_exists('layout'))
		{
			$this->set_param('layout', 'Index');
		}
		$module = $this->get_param('module');
		$controller = $this->get_param('controller');
		$action = $this->get_param('action');

		$layout = $this->get_param('layout');

		$controller_reflection = new \ReflectionClass('\\'.$module.'\\Controller\\'.$controller);
		$controller_instance = $controller_reflection->newInstance($this);
		call_user_func(array($controller_instance, 'init'));
		call_user_func(array($controller_instance, 'before_action'));
		call_user_func(array($controller_instance, 'action_'.strtolower($action)));
		call_user_func(array($controller_instance, 'after_action'));

		if ($controller_instance->get_layout() !== NULL)
		{
			$layout = $controller_instance->get_layout();
		} else {
			$layout_class = '\\'.$module.'\\Layout\\'.$layout;
			if (class_exists($layout_class))
			{
				$layout = new $layout_class;
			} else {
				$layout = new \Template\Classes\Layout;
			}
		}
		$view = $controller_instance->get_view();

		$renderer = $this->get_renderer();
		$layout->set_content($renderer->render($view));
		return $renderer->render($layout);
	}

	/**
	 * @return \Renderer\Classes\AbstractRenderer
	 */
	public function get_renderer()
	{
		if (isset($this['renderer']))
		{
			$renderer_class_name = '\\Renderer\\Classes\\'.$this['renderer'];
		}
		else
		{
			$renderer_class_name = '\\Renderer\Classes\Html';
		}

		$renderer_reflection = new \ReflectionClass($renderer_class_name);

		return $renderer_reflection->newInstance();
	}

} 