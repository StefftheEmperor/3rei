<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 16.11.14
 * Time: 17:27
 */
namespace Request\Classes;

use Request\Classes\Controller\Exception;
use Request\Classes\Rewrite\Params;
use Request\Layout\Plain;
use Request\Model\Request\Attribute;
use Request\Model\Request\Param;
use Request\Model\Rewrite;
use Model\Classes\AbstractModel;
use Db\Classes\AbstractConnection;
use Request\Classes\Url;
use Template\Classes\Layout;

class Request extends AbstractModel {

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
	public static function factory_by_url(AbstractConnection $connection, Url $url)
	{
		$request = new static($url);
		$rewrite_model = Rewrite::factory_by_url($connection, $url);
		$request_model = $rewrite_model->get_request_model();

		$request->set_model($request_model);
		return $request;
	}

	/**
	 * @param $id
	 * @return static
	 */
	public static function factory_by_id(AbstractConnection $connection, $id)
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
	public function set_url(Url $url)
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

			$this->url = $rewrite->get_rewritten_url($this);
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
		elseif (isset($model))
		{
			return $model->get_params();
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
				return Attribute::factory($offset, NULL);
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

	public function set_params(Params $params = NULL)
	{
		if (isset($params)) {
			foreach ($params as $param_key => $param_value) {
				$this->set_param($param_key, $param_value);
			}
		}
		return $this;
	}

	/**
	 * @param $offset
	 * @return bool
	 */
	public function attribute_exists($offset)
	{
		$model = $this->get_model();
		if (isset($model)) {
			return $this->get_model()->attribute_exists($offset);
		} else {
			return (isset($this->attributes[$offset]));
		}
	}

	public function has_attribute($offset)
	{
		return $this->attribute_exists($offset);
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
		if (isset($value) AND ! $value instanceof \Request\Interfaces\Request\Attribute)
		{
			$value = Attribute::factory($offset, $value);
		}
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
		$child_request = new static;
		$child_request->set_model($child_model);
		$child_request->set_post_data($this->get_post_data());

		return $child_request;
	}
	/**
	 * @return mixed
	 */
	public function execute()
	{
		if ( ! ($this->attribute_exists('module') AND $this->attribute_exists('controller') AND $this->attribute_exists('action')))
		{
			$this->set_attribute('module', 'Error');
			$this->set_attribute('controller', 'Error');
			$this->set_attribute('action', 'Error_404');
			$this->set_attribute('view', NULL);
		}

		if ( ! $this->attribute_exists('layout'))
		{
			$this->set_attribute('layout', 'Index');
		}
		$module = $this->get_attribute('module');
		$controller = $this->get_attribute('controller');
		$action = $this->get_attribute('action');

		$layout = $this->get_attribute('layout');

		$str_module = $module->get_value();
		$str_controller = $controller->get_value();
		$controller_reflection = new \ReflectionClass('\\'.$str_module.'\\Controller\\'.$str_controller);
		$controller_instance = $controller_reflection->newInstance($this);
		if ($controller_instance->get_layout() !== NULL)
		{
			$layout = $controller_instance->get_layout();
		} else {
			$layout_class = '\\'.$module->get_value().'\\Layout\\'.$layout->get_value();
			if (class_exists($layout_class))
			{
				$layout = new $layout_class;
			} else {
				$layout = new Layout;
			}
		}
		$controller_instance->set_layout($layout);
		call_user_func(array($controller_instance, 'init'));
		call_user_func(array($controller_instance, 'before_action'));
		$action_method = 'action_'.strtolower($action->get_value());
		if (method_exists($controller_instance, $action_method)) {
			call_user_func(array($controller_instance, $action_method));
		}
		else
		{
			throw new Exception('Action '.$action_method.' does not exist in controller '.$str_controller);
		}
		call_user_func(array($controller_instance, 'after_action'));


		$view = $controller_instance->get_view();
		$renderer = $this->get_renderer();
		$view->set_renderer($renderer);

		$layout = $controller_instance->get_layout();
		if ( ! isset($layout))
		{
			$layout = new Plain;
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