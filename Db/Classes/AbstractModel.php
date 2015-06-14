<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 30.11.14
 * Time: 17:07
 */

namespace Db\Classes;

/**
 * Class AbstractModel
 * @package Db\Classes
 */
abstract class AbstractModel
	extends \Model\Classes\AbstractModel
	implements \Db\Interfaces\Model
{

	use \Db\Traits\Model;

	/**
	 * @var null
	 */
	protected $primary_key = NULL;

	/**
	 * @var bool
	 */
	protected $is_new = TRUE;

	/**
	 *
	 */
	protected function init()
	{

	}

	/**
	 * @param null $value
	 * @return $this|bool
	 */
	public function is_new($value = NULL)
	{
		if (isset($value))
		{
			$this->is_new = $value;

			return $this;
		} else {
			return $this->is_new;
		}
	}

	/**
	 * @return mixed
	 */
	public function get_primary_key()
	{
		$primary_key = $this->get_table()->get_primary_key();

		return $primary_key;
	}

	/**
	 * @return mixed
	 */
	public function get_primary_id()
	{
		$primary_id = $this->{'get_'.strtolower($this->get_primary_key())}();
		return $primary_id;
	}

	/**
	 * @param $value
	 * @return \Model\Classes\AbstractModel
	 */
	public function set_primary_key($value)
	{
		$primary_key = $this->get_table()->get_primary_key();

		return $this->__set($primary_key, $value);
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function set_primary_id($value)
	{
		return $this->{'set_'.strtolower($this->get_primary_key())}($value);
	}

	/**
	 * @return Mysql\Statement
	 */
	public function save()
	{
		if ($this->is_new()) {
			$query = new Mysql\Query($this->get_connection(), Mysql\Query::QUERY_INSERT, $this);
		} else {
			$filter = Filter\Comparison::factory($this->get_table()->get_primary_key(), $this->__get($this->get_table()->get_primary_key()));
			$query = new Mysql\Query($this->get_connection(), \Db\Classes\Mysql\Query::QUERY_UPDATE, $this);
			$query->set_filter($filter);
		}

		$query->set_table($this->get_table());

		$result = $query->execute()->current();

		$this->__set($this->get_table()->get_primary_key(), $result->__get($this->get_table()->get_primary_key()));
		return $result;
	}

	/**
	 * @param mixed $key
	 * @return mixed|null
	 * @throws \Debug\Classes\CustomException
	 */
	public function offsetGet($key)
	{

		if (array_key_exists($key, $this->get_table()->get_foreign_keys_map()))
		{
			$foreign_key = $this->get_table()->get_foreign_key($key);
			return call_user_func(array($foreign_key->get_foreign_model(), 'factory_by_'.$foreign_key->get_foreign_key()), parent::offsetGet($foreign_key->get_key()));
		}
		else
		{
			return parent::offsetGet($key);
		}
	}

	/**
	 * @return $this
	 */
	public final function before_sleep()
	{
		$this->get_table()->before_sleep();

		return $this;
	}
} 