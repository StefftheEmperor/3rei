<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 09.09.14
 * Time: 18:16
 */

namespace Db\Classes;


class Table {
	protected $table_name;

	protected $columns = null;

	protected $filter = NULL;

	protected $result;

	protected $database;

	protected $primary_key;

	protected $foreign_keys_map = array();
	/**
	 * @param $table_name
	 */
	public function __construct($table_name)
	{
		$this->table_name = $table_name;

		$this->init_foreign_keys_map();
		$this->get_columns();
	}

	public function init_foreign_keys_map()
	{

	}

	public function get_primary_key()
	{
		return $this->primary_key;
	}


	public function set_primary_key($primary_key)
	{
		$this->primary_key = $primary_key;
		return $this;
	}

	public function get_foreign_keys_map()
	{
		return $this->foreign_keys_map;
	}

	public function get_foreign_key($key)
	{
		if (array_key_exists($key, $this->get_foreign_keys_map()))
		{
			return $this->foreign_keys_map[$key];
		}
		else
		{
			return NULL;
		}
	}

	public function get_foreign_model($key)
	{
		if (array_key_exists($key, $this->get_foreign_keys_map()))
		{
			return $this->foreign_keys_map[$key]->get_foreign_model();
		}
		else
		{
			return NULL;
		}
	}

	public function add_foreign_key(\Db\Classes\ForeignKey $foreignKey)
	{
		$this->foreign_keys_map[$foreignKey->get_key()] = $foreignKey;
	}

	/**
	 * @param $table_name
	 * @return $this
	 */
	public static function factory($table_name)
	{
		$instance = new static($table_name);

		return $instance;
	}

	/**
	 * @param $filter
	 * @return $this
	 */
	public function filter($filter)
	{
		$this->filter = $filter;

		return $this;
	}

	/**
	 * @return \Db\Mysql\Result
	 */
	public function get_one()
	{
		return $this->get_database()->select()->from($this->table_name)->filter($this->filter)->execute()->current();
	}

	public function get_all()
	{
		return $this->get_database()->select()->from($this->table_name)->filter($this->filter)->execute();
	}

	/**
	 * @return \Db\Mysql\Connection
	 */
	public function get_database()
	{
		if ( ! isset($this->database))
		{
			$this->database = \Model\Classes\Registry::get('database');
		}

		return $this->database;
	}

	/**
	 *
	 */
	public function get_result()
	{
		if ( ! isset($this->result)) {

		}
	}
	public function get_columns()
	{
		if ($this->columns === NULL)
		{
			$db = $this->get_database();
			$this->columns = $db->describe_table($this)->map_to('\Db\Classes\Column');
		}

		return $this->columns;
	}

	public function add_column($column)
	{

		$this->columns[$column->get_field()] = $column;
	}

	public function get_table_name()
	{
		return $this->table_name;
	}

	public final function before_sleep()
	{
		unset($this->database);
	}

} 