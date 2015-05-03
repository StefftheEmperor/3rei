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

	protected $request = NULL;
	/**
	 * @param \Request\Classes\Url $url
	 * @return \Request\Model\Rewrite
	 */
	public static function factory_by_url(\Request\Classes\Url $url = NULL)
	{
		if (isset($url))
		{
			$filter = new \Db\Classes\Filter(\Db\Classes\Expression\Row::factory('url'), '=', \Db\Classes\Expression\Value::factory($url->get_absolute_url()));
			$result = \Request\Model\Rewrite\Table::factory('rewrite')->filter($filter)->get_one();

			return $result->map_to(get_called_class());
		}
		else
		{
			$instance = new static;
			$instance->set_table(\Request\Model\Rewrite\Table::factory('rewrite'));
			return $instance;
		}
	}

	public function get_request()
	{
		if ( ! isset($this->request))
		{
			if ($this->offsetExists('request_id'))
			{
				$request_id = $this->__get('request_id');
				$this->request = \Request\Model\Request::factory_by_id($request_id);
			}
			else
			{
				$this->request = new \Request\Model\Request;
			}
		}

		return $this->request;
	}

	public function save()
	{
		$this->get_request()->save();

		parent::save();
	}
	protected function init_table_model()
	{
		$this->table_model = '\Request\Model\Rewrite\Table';
	}
} 