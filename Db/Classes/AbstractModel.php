<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 30.11.14
 * Time: 17:07
 */

namespace Db\Classes;

class AbstractModel extends \Model\Classes\AbstractModel {

	private $table = NULL;
	protected $table_name = NULL;
	protected $primary_key = NULL;

	protected $is_new = TRUE;
	final public function __construct()
	{
		$this->init_table();
		if (($this->get_table() === NULL) OR ($this->get_table()->get_primary_key() === NULL))
		{
			throw new Exception('Primary id needs to be set in Class '.get_called_class());
		}
		$this->init();
	}

	protected function init()
	{

	}

	protected function init_table()
	{
		if (isset($this->table_name))
		{
			$this->table = new Table($this->table_name);
			if (isset($this->primary_key))
			{
				$this->table->set_primary_key($this->primary_key);
			}
		}
	}

	public function set_table(\Db\Table $table)
	{
		foreach ($table->get_columns() as $column)
		{
			$this->add_available_key($column->get_field());
		}

		$this->table = $table;
		return $this;
	}

	public function get_table()
	{
		return $this->table;
	}

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

	public function get_primary_key()
	{
		$primary_key = $this->get_table()->get_primary_key();

		return $this->__get($primary_key);
	}

	public function get_primary_id()
	{
		return $this->{'get_'.strtolower($this->get_primary_key())}();

	}
	
	public function set_primary_key($value)
	{
		$primary_key = $this->get_table()->get_primary_key();

		return $this->__set($primary_key, $value);
	}

	public function set_primary_id($value)
	{
		return $this->{'set_'.strtolower($this->get_primary_key())}($value);
	}

	public function save()
	{
		$filter = new Filter($this->get_table()->get_primary_key(), '=', $this->__get($this->get_table()->get_primary_key()));
		if ($this->is_new()) {
			$query = new \Db\Classes\Mysql\Query(\Model\Classes\Registry::get('database'), \Db\Classes\Mysql\Query::QUERY_INSERT, $this);
		} else {
			$query = new \Db\Classes\Mysql\Query(\Model\Classes\Registry::get('database'), \Db\Classes\Mysql\Query::QUERY_UPDATE, $this);
		}

		$query->set_table($this->get_table())->set_filter($filter);

		$query->execute();
	}


	public final function before_sleep()
	{
		$this->get_table()->before_sleep();
	}
} 