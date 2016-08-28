<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 30.11.14
 * Time: 16:27
 */

namespace Request\Model;

use Db\Classes\AbstractModel;
use Db\Classes\Filter\Regexp;
use Db\Interfaces\Connection;
use Db\Classes\Expression\Value;
use Request\Classes\Rewrite\Params;
use Request\Classes\Url;
use Db\Classes\Expression\Row;
use Db\Classes\Table\Select\All;
use Db\Classes\Filter\Comparison;
use Request\Interfaces\Request\Attributes;
use Request\Model\Rewrite\Table;
use Request\Model\Request;

class Rewrite extends AbstractModel
{
	protected $primary_key = 'id';
	protected $table_name = 'rewrite';

	protected $request_model = NULL;

	protected $params = NULL;

	protected $param_keys = NULL;

	public function init()
	{
		parent::init();

		$this->params = new Params();
	}

	/**
	 * @param \Request\Classes\Url $url
	 * @return \Request\Model\Rewrite
	 */
	public static function factory_by_url(Connection $connection, Url $url = NULL)
	{
		if (isset($url))
		{
			$filter = Regexp::factory(Value::factory($url->get_url()), Row::factory('regexp_pattern'));
			$result = $connection->get_model_reflection('\Request\Model\Rewrite\Table')->factory('rewrite')->filter($filter)->get_one(All::factory());

			$rewrite = $result->map_to(get_called_class());
			$rewrite->parse_params($url);
			return $rewrite;
		}
		else
		{
			$instance = new static;
			$instance->set_table(Table::factory('rewrite'));
			return $instance;
		}
	}

	/**
	 * @param $id
	 * @return \Request\Model\Rewrite
	 */
	public static function factory_by_id(Connection $connection, $id)
	{
		$filter = Comparison::factory(Row::factory('id'),Value::factory($id));
		$result = $connection->get_model_reflection('\Request\Model\Rewrite\Table')->factory('rewrite')->filter($filter)->get_one(\Db\Classes\Table\Select\All::factory());

		$rewrite = $result->map_to(get_called_class());

		return $rewrite;
	}

	/**
	 * @param Request $request
	 * @return static
	 */
	public static function factory_by_request(Connection $connection, \Request\Model\Request $request)
	{
		if ($request->__isset('id')) {
			$filter = Comparison::factory(Row::factory('request_id'), Value::factory($request->get_id()));
			$result = $connection->get_model_reflection('\Request\Model\Rewrite\Table')->factory('rewrite')->filter($filter)->get_one(All::factory());

			$rewrite = $result->map_to(get_called_class());
		} else {
			$rewrite = new static($connection);
		}
		return $rewrite;
	}

	public function parse_url()
	{
		$matches = $params = array();
		if ( ! $this->__isset('url'))
		{
			return;
		}

		$rewrite_url = $this->__get('url');
		preg_match_all('/\$([a-zA-Z0-9_\-]*)\$/', $rewrite_url, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

		$url_pattern = '^';
		$end = 0;
		if (count($matches) > 0)
		{
			$matches_iterator = 0;
			foreach ($matches as $match_key => $match)
			{
				$subject = $match[0][0];
				$params[$match_key] = $match[1][0];
				if ($matches_iterator == 0)
				{
					$url_pattern .= substr($rewrite_url, 0, $match[0][1]);
				} else {
					$url_pattern .= substr($rewrite_url, $end, $match[0][1] - $end);
				}
				$url_pattern .= '([a-zA-Z0-9_\-]*)';
				$end = $match[0][1] + strlen($match[0][0]);
				$matches_iterator++;
			}
		}
		$url_pattern .= substr($rewrite_url, $end).'$';

		$this->__set('regexp_pattern', $url_pattern);

		$this->param_keys = $params;


	}

	public function set_url($url)
	{
		parent::__set('url', $url);

		$this->parse_url();
	}

	public function get_rewritten_url($source)
	{
		$rewrite_url = $this->get_url();

		$url = '';

		$matches = array();
		preg_match_all('/\$([a-zA-Z0-9_\-]*)\$/', $rewrite_url, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

		$end = 0;
		if (count($matches) > 0)
		{
			$matches_iterator = 0;
			foreach ($matches as $match_key => $match) {
				$subject = $match[1][0];

				if ($matches_iterator == 0) {
					$url .= substr($rewrite_url, 0, $match[0][1]);
				} else {
					$url .= substr($rewrite_url, $end, $match[0][1] - $end);
				}

				if ($source instanceof Attributes)
				{
					if (isset($source) AND isset($source[$subject])) {
						$url .= $source[$subject];
					}
				}
				elseif ($source instanceof \Request\Classes\Request)
				{
					if ($source->has_attribute($subject))
					{
						$url .= $source->get_attribute($subject)->get_value();
					}
				}
				$end = $match[0][1] + strlen($match[0][0]);
				$matches_iterator++;
			}
		}
		else
		{
			$url = $rewrite_url;
		}

		return $url;
	}

	public function parse_params(Url $url = NULL)
	{
		$matches = $params = array();
		if ( ! $this->__isset('url'))
		{
			return;
		}

		$url_string = $url->get_url();

		$this->parse_url();

		$url_matches = array();
		$regexp_pattern = $this->__get('regexp_pattern');
		preg_match_all('/'.str_replace(array('/'),array('\/'), $regexp_pattern).'/', $url_string, $url_matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

		if (count($url_matches) > 0) {
			foreach ($url_matches[0] as $url_match_key => $url_match) {
				if ($url_match_key > 0)
				{
					$attribute_key = $this->param_keys[$url_match_key - 1];
					$attribute_value = $url_match[0];
					$this->set_param($attribute_key, Request\Attribute::factory($attribute_key, $attribute_value));
				}
			}
		}
	}

	/**
	 * @return Params
	 */
	public function get_params()
	{
		if ( ! isset($this->params))
		{
			$this->params = new Params;
		}
		return $this->params;
	}

	public function set_param($offset, $value)
	{
		$this->get_params()->offsetSet($offset, $value);
	}
	/**
	 * @param Connection $connection
	 * @return mixed
	 */
	public static function get_all(Connection $connection)
	{
		$result = $connection->get_model_reflection('\Request\Model\Rewrite\Table')->factory('rewrite')->select(\Db\Classes\Table\Select\All::factory())->get_all();

		return $result->map_to(get_called_class());
	}

	/**
	 * @return null|\Request\Model\Request
	 */
	public function get_request_model()
	{
		if ( ! isset($this->request_model))
		{
			if ($this->offsetExists('request_id'))
			{
				$request_id = $this->__get('request_id');
				$this->request_model = \Request\Model\Request::factory_by_id($this->get_connection(), $request_id);
			}
			else
			{
				$this->request_model = new \Request\Model\Request($this->get_connection());
			}
		}

		$this->request_model->set_attributes($this->params);
		return $this->request_model;
	}

	public function get_table_name()
	{
		return $this->table_name;
	}

	public function save()
	{
		$this->get_request_model()->save();

		$this->set_request_id($this->get_request_model()->get_id());
		parent::save();
		return $this;
	}

	protected function init_table_model()
	{
		$this->table_model = '\Request\Model\Rewrite\Table';
	}
} 