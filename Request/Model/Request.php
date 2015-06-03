<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 01.02.15
 * Time: 17:27
 */

namespace Request\Model;


class Request extends \Db\Classes\AbstractModel implements \Db\Interfaces\Model {
	protected $primary_key = 'id';
	protected $table_name = 'request';

	protected $params = NULL;

	protected $attributes = array();
	public function get_primary_key()
	{
		return $this->primary_key;
	}

	public function get_table_name()
	{
		return $this->table_name;
	}

	public static function factory_by_id(\Db\Interfaces\Connection $connection, $id)
	{
		$request = new static($connection);
		$filter = \Db\Classes\Filter::factory(\Db\Classes\Expression\Row::factory($request->get_primary_key()), '=', \Db\Classes\Expression\Value::factory($id));
		$result = \Request\Model\Rewrite\Table::factory($connection, $request->get_table_name())->filter($filter)->get_one();

		return $result->map_to($request);
	}

	public function get_params()
	{
		if ( ! isset($this->params))
		{
			$this->load_params();
		}

		return $this->params;
	}

	public function get_param($key)
	{
		$params = $this->get_params();

		foreach ($params as $param)
		{
			if ($param->get_key() == $key)
			{
				return $param->get_value();
			}
		}

		return NULL;
	}

	public function param_exists($key)
	{
		$params = $this->get_params();

		foreach ($params as $param)
		{
			if ($param->get_key() == $key)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	public function set_param($key, $value)
	{
		$found = FALSE;
		$params = $this->get_params();
		foreach ($params as $param_key => $param)
		{
			if ($param->get_key() == $key)
			{
				if (isset($value))
				{
					$param->set_value($value);
				} else {
					unset($this->params[$param_key]);
				}
				$found = TRUE;
			}
		}

		if ( ! $found)
		{
			if (isset($value)) {
				$param = new \Request\Model\Request\Param($this->get_connection());
				$param->set_key($key);
				$param->set_value($value);

				$this->params[] = $param;
			}
		}

		return $this;
	}

	public function set_params(array $params)
	{
		foreach ($params as $param_key => $param_value)
		{
			$this->set_param($param_key, $param_value);
		}

		return $this;
	}

	public function load_params()
	{
		$params = array();
		if ($this->offsetExists('id')) {
			$result_set = \Db\Classes\Table::factory($this->get_connection(), 'request__request2params')->filter(\Db\Classes\Filter::factory(\Db\Classes\Mysql\Expression\Row::factory('request_id'), '=', \Db\Classes\Mysql\Expression\Value::factory($this->get_id())))->get_all();
			foreach ($result_set as $result_row)
			{
				$params[] = \Request\Model\Param\Table::factory($this->get_connection(), 'request__param')->filter(\Db\Classes\Filter::factory(\Db\Classes\Mysql\Expression\Row::factory('id'), '=', \Db\Classes\Mysql\Expression\Value::factory($result_row['param_id'])))->get_one()->map_to('\Request\Model\Request\Param');
			}
		}

		$this->params = $params;
	}

	public function set_attribute($offset, $value)
	{
		$this->attributes[$offset] = $value;

		return $this;
	}

	public function set_attributes($attributes)
	{
		$this->attributes = $attributes;

		return $this;
	}

	public function get_attribute($offset)
	{
		if (isset($this->attributes[$offset]))
		{
			return $this->attributes[$offset];
		}
		else
		{
			return NULL;
		}

	}

	public function save()
	{
		foreach ($this->get_params() as $param)
		{
			$param->save();
		}

		parent::save();
	}
}