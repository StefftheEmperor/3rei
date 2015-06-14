<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 16.11.14
 * Time: 17:27
 */
namespace Request\Classes;
use Request\Model\Rewrite;

class Request extends \Model\Classes\AbstractModel {

	/**
	 * @var \Request\Classes\Url
	 */
	protected $url = NULL;

	/**
	 * @var \Request\Model\Request
	 */
	protected $model = NULL;
	protected $params = NULL;
	protected $attributes = NULL;
	protected $post_data = NULL;
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
	public static function factory_by_url(\Db\Classes\AbstractConnection $connection, \Request\Classes\Url $url)
	{
		$request = new static($url);
		$rewrite_model = \Request\Model\Rewrite::factory_by_url($connection, $url);
		$request_model = $rewrite_model->get_request_model();

		$request->set_model($request_model);
		return $request;
	}

	/**
	 * @param $id
	 * @return static
	 */
	public static function factory_by_id(\Db\Classes\AbstractConnection $connection, $id)
	{
		$request = new static;
		$model = \Request\Model\Request::factory_by_id($connection, $id);

		$request->set_model($model);

		return $request;
	}

	/**
	 * @param \Request\Model\Request $request_model
	 * @return static
	 */
	public static function factory_by_model(\Request\Model\Request $request_model)
	{
		$request = new static;
		$request->set_model($request_model);

		return $request;
	}

	/**
	 * @param Url $url
	 * @return $this
	 */
	public function set_url(\Request\Classes\Url $url)
	{
		$this->url = $url;

		return $this;
	}

	/**
	 * @return Url
	 */
	public function get_url()
	{
		if ( ! isset($this->url))
		{
			$rewrite = Rewrite::factory_by_request($this->get_model()->get_connection(), $this->get_model());
			$this->url = $rewrite->get_url();
		}
		return $this->url;
	}

	/**
	 * @param \Request\Model\Request $request_model
	 * @return $this
	 */
	public function set_model(\Request\Model\Request $request_model)
	{
		$this->model = $request_model;

		return $this;
	}

	/**
	 * @return \Request\Model\Request
	 */
	public function get_model()
	{
		return $this->model;
	}

	/**
	 * @return $this
	 */
	public function save_rewrite()
	{

		$this->get_url()->get_rewrite()->save();

		return $this;
	}

	/**
	 * @return null
	 */
	public function get_params()
	{
		$model = $this->get_model();
		if ( ! isset($this->params) AND ! isset($model))
		{
			$this->load_params();
		}

		return $this->params;
	}

	/**
	 * @return $this
	 */
	public function load_params()
	{
		$model = $this->get_model();
		if (isset($model)) {
			$this->get_model()->load_params();
		}
		return $this;
	}

	/**
	 * @param $key
	 * @return null
	 */
	public function get_param($offset)
	{
		$model = $this->get_model();
		if (isset($model)) {
			return $this->get_model()->get_param($offset);
		} else {
			if (isset($this->params[$offset]))
			{
				return $this->params[$offset];
			} else {
				return NULL;
			}
		}
	}

	/**
	 * @param $key
	 * @param $value
	 *
	 * @return $this
	 */
	public function set_param($offset, $value)
	{
		$model = $this->get_model();
		if (isset($model)) {
			$model->set_param($offset, $value);
		} else {
			$this->params[$offset] = $value;
		}
		return $this;
	}

	public function set_params(Array $params = NULL)
	{
		if (isset($params)) {
			foreach ($params as $param_key => $param_value) {
				$this->set_param($param_key, $param_value);
			}
		}
		return $this;
	}


	public function get_attribute($offset)
	{
		$model = $this->get_model();
		if (isset($model))
		{
			return $this->get_model()->get_attribute($offset);
		} else {
			if (isset($this->attributes[$offset]))
			{
				return $this->attributes[$offset];
			} else {
				return NULL;
			}
		}
	}

	public function get_attributes()
	{
		$model = $this->get_model();
		if (isset($model))
		{
			return $this->get_model()->get_attributes();
		} else {
			return $this->attributes;
		}
	}

	public function set_attribute($offset, $value)
	{
		$model = $this->get_model();
		if (isset($model)) {
			$this->get_model()->set_attribute($offset, $value);
		} else {
			$this->attributes[$offset] = $value;
		}

		return $this;
	}

	public function set_attributes($attributes)
	{
		foreach ($attributes as $attribute_key => $attribute_value)
		{
			$this->set_attribute($attribute_key, $attribute_value);
		}
	}
	/**
	 * @param $offset
	 * @return bool
	 */
	public function param_exists($offset)
	{
		$model = $this->get_model();
		if (isset($model)) {
			return $this->get_model()->param_exists($offset);
		} else {
			return (isset($this->params[$offset]));
		}
	}

	public function get_post_data()
	{
		if ( ! isset($this->post_data))
		{
			$this->post_data = new \Request\Classes\Request\Post;
		}

		return $this->post_data;
	}

	public function set_post_data($post_data, $post_value = NULL)
	{
		if ((is_array($post_data) OR ($post_data instanceof \Request\Classes\Request\Post)) AND ! isset($post_value))
		{
			foreach ($post_data as $post_data_key => $post_data_value)
			{
				$this->set_post_data($post_data_key, $post_data_value);
			}
		} elseif (is_scalar($post_data) AND isset($post_value))
		{
			if (is_numeric($post_value))
			{
				$post_value = floatval($post_value);
				if (intval($post_value) == $post_value)
				{
					$post_value = intval($post_value);
				}
			} elseif (is_string($post_value) AND $post_value === '')
			{
				$post_value = NULL;
			}
			$this->get_post_data()->__set($post_data , $post_value);
		} else {
			throw new \Request\Classes\Request\Exception('Can\'t handle request to set post_data');
		}

		return $this;
	}

	public function get_child_request()
	{
		$child_model = new \Request\Model\Request($this->get_model()->get_connection());
		$child_request = new \Request\Classes\Request;
		$child_request->set_model($child_model);
		$child_request->set_post_data($this->get_post_data());

		return $child_request;
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
			$this->set_param('view', NULL);
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
		$controller_instance->set_layout($layout);
		call_user_func(array($controller_instance, 'init'));
		call_user_func(array($controller_instance, 'before_action'));
		$action_method = 'action_'.strtolower($action);
		if (method_exists($controller_instance, $action_method)) {
			call_user_func(array($controller_instance, $action_method));
		}
		else {
			throw new \Request\Classes\Controller\Exception('Action '.$action.' does not exist in controller '.$controller);
		}
		call_user_func(array($controller_instance, 'after_action'));


		$view = $controller_instance->get_view();
		$renderer = $this->get_renderer();
		$view->set_renderer($renderer);

		$layout = $controller_instance->get_layout();
		if ( ! isset($layout))
		{
			$layout = new \Request\Layout\Plain;
		}

		$layout->set_controller($controller_instance);
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