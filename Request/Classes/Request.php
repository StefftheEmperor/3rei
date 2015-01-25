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

	public function __construct($url = NULL)
	{
		if (isset($url)) {
			$this->set_url($url);
		}
	}

	public static function factory_by_url(\Request\Classes\Url $url)
	{
		$request = new static;
		$request->set_url($url);

		$url->get_rewrite()->set_request($request);
		return $request;
	}

	public function set_url(\Request\Classes\Url $url)
	{
		$this->url = $url;

	}

	public function get_url()
	{
		return $this->url;
	}

	public function save_rewrite()
	{

		$this->get_url()->get_rewrite()->save();
	}
	public function execute()
	{

		$module = $this->get_module();
		$controller = $this->get_controller();
		$action = $this->get_action();

		$controller_reflection = new \ReflectionClass('\\'.$module.'\\Controller\\'.$controller);
		$controller_instance = $controller_reflection->newInstance($this);

		call_user_func(array($controller_instance, 'action_'.strtolower($action)));

		$view = $controller_instance->get_view();

		$renderer = $this->get_renderer();

		return $renderer->render($view);
	}

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