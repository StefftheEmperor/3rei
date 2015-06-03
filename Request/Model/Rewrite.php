<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 30.11.14
 * Time: 16:27
 */

namespace Request\Model;

class Rewrite extends \Db\Classes\AbstractModel {

	protected $primary_key = 'id';
	protected $table_name = 'rewrite';

	protected $request_model = NULL;

	protected $params = NULL;
	/**
	 * @param \Request\Classes\Url $url
	 * @return \Request\Model\Rewrite
	 */
	public static function factory_by_url(\Db\Interfaces\Connection $connection, \Request\Classes\Url $url = NULL)
	{
		if (isset($url))
		{
			$filter = new \Db\Classes\Filter(\Db\Classes\Expression\Value::factory($url->get_url()), 'REGEXP', \Db\Classes\Expression\Row::factory('regexp_pattern'));
			$result = $connection->get_model_reflection('\Request\Model\Rewrite\Table')->factory('rewrite')->filter($filter)->get_one();

			$rewrite = $result->map_to(get_called_class());
			$rewrite->parse_params($url);
			return $rewrite;
		}
		else
		{
			$instance = new static;
			$instance->set_table(\Request\Model\Rewrite\Table::factory('rewrite'));
			return $instance;
		}
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public static function factory_by_id(\Db\Interfaces\Connection $connection, $id)
	{
		$filter = new \Db\Classes\Filter(\Db\Classes\Expression\Row::factory('id'),'=',\Db\Classes\Expression\Value::factory($id));
		$result = $connection->get_model_reflection('\Request\Model\Rewrite\Table')->factory('rewrite')->filter($filter)->get_one();

		$rewrite = $result->map_to(get_called_class());

		return $rewrite;
	}

	/**
	 * @param Request $request
	 * @return static
	 */
	public function factory_by_request(\Request\Model\Request $request)
	{
		$filter = new \Db\Classes\Filter(\Db\Classes\Expression\Row::factory('request_id'),'=',\Db\Classes\Expression\Value::factory($request->get_id()));
		$result = $connection->get_model_reflection('\Request\Model\Rewrite\Table')->factory('rewrite')->filter($filter)->get_one();

		$rewrite = $result->map_to(get_called_class());

		return $rewrite;
	}

	public function parse_params(\Request\Classes\Url $url = NULL)
	{
		$matches = $params = array();
		$rewrite_url = $this->__get('url');
		preg_match_all('/\$([a-zA-Z0-9_\-]*)\$/', $rewrite_url, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
		$url_string = $url->get_url();
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
		$url_matches = array();
		preg_match_all('/'.str_replace(array('/'),array('\/'),$url_pattern).'/', $url_string, $url_matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

		if (count($url_matches) > 0) {
			foreach ($url_matches[0] as $url_match_key => $url_match) {
				if ($url_match_key > 0) {
					$this->params[$params[$url_match_key - 1]] = $url_match[0];
				}
			}
		}
	}

	public static function get_all(\Db\Interfaces\Connection $connection)
	{
		$result = $connection->get_model_reflection('\Request\Model\Rewrite\Table')->factory('rewrite')->get_all();

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

	public function save()
	{
		$this->get_request_model()->save();

		parent::save();
	}
	protected function init_table_model()
	{
		$this->table_model = '\Request\Model\Rewrite\Table';
	}
} 