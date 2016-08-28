<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 01.02.15
 * Time: 17:27
 */

namespace Request\Model;


use Db\Classes\Filter\Comparison;
use Db\Classes\Mysql\Expression\Row;
use Db\Classes\Mysql\Expression\Value;
use Db\Classes\Table\Select\All;
use Request\Classes\Request\Attribute\Exception;
use Request\Classes\Rewrite\Attributes;
use Request\Classes\Rewrite\Params;
use Request\Interfaces\Request\Attribute;
use Request\Model\Request\Param;
use Request\Model\Request\Param\Table;
use Request\Model\Request as Request_Model;

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
		$filter = \Db\Classes\Filter\Comparison::factory(\Db\Classes\Expression\Row::factory($request->get_primary_key()), \Db\Classes\Expression\Value::factory($id));
		$result = \Request\Model\Rewrite\Table::factory($connection, $request->get_table_name())->filter($filter)->get_one(\Db\Classes\Table\Select\All::factory());

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
				return $param;
			}
		}

		return \Request\Model\Request\Attribute::factory($key, NULL);
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
			$param_key = $param->get_key();
			if ($param_key === $key)
			{
				if (isset($value))
				{
					if ($value instanceof Attribute)
					{
						$param->set_value($param->get_value());
					}
					else
					{
						$param->set_value($value);
					}
				} else {
					unset($this->params[$param_key]);
				}
				$found = TRUE;
			}
		}

		if ( ! $found)
		{
			if (isset($value)) {
				$param = new Param($this->get_connection());
				$param->set_key($key);
				$param->set_value($value);

				$this->params->offsetSet($key, $param);
			}
		}

		return $this;
	}

	public function set_params(Params $params)
	{
		foreach ($params as $param_key => $param_value)
		{
			$this->set_param($param_key, $param_value);
		}

		return $this;
	}

	public function load_params()
	{
		$params = new Params();
		if ($this->offsetExists('id')) {
			$result_set = \Db\Classes\Table::factory($this->get_connection(), 'request__request2params')->filter(Comparison::factory(Row::factory('request_id'), Value::factory($this->get_id())))->get_all(All::factory());
			foreach ($result_set as $result_row)
			{
				$param = Table::factory($this->get_connection(), 'request__param')->filter(Comparison::factory(Row::factory('id'), Value::factory($result_row['param_id'])))->get_one(All::factory())->map_to('\Request\Model\Request\Param');
				$params->offsetSet($param->get_key(), $param);
			}
		}

		$this->params = $params;
	}

	public function set_attribute($offset, $value)
	{
		$this->get_attributes()->offsetSet($offset, $value);

		return $this;
	}

	public function set_attributes($attributes)
	{
		if ( ! $attributes instanceof Attributes)
		{
			throw new Exception;
		}
		if ($attributes instanceof Params)
		{
			$this->attributes = new Attributes;
			foreach ($attributes as $attribute_key => $attribute_value)
			{
				$this->attributes->offsetSet($attribute_key, $attribute_value);
			}
		}
		else
		{
			$this->attributes = $attributes;
		}
		return $this;
	}

	public function attribute_exists($offset)
	{
		return (isset($this->attributes[$offset]) OR $this->param_exists($offset));
	}

	public function get_attribute($offset)
	{
		if (isset($this->attributes[$offset]))
		{
			return $this->attributes[$offset];
		}
		elseif ($this->param_exists($offset))
		{
			return $this->get_param($offset);
		}
		else
		{
			return NULL;
		}
	}

	public function get_attributes()
	{
		return $this->attributes;
	}

	public function save()
	{
		foreach ($this->get_params() as $param)
		{
			$param->save();
		}

		return parent::save();
	}
}