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

	protected $joined_tables = NULL;

	protected $alias = NULL;

	protected $group = NULL;

	protected $select = NULL;

	protected $order = NULL;

	public $debug = NULL;
	/**
	 * @param $table_name
	 */
	public function __construct(\Db\Classes\Mysql\Connection $connection, $table_name)
	{
		$this->database = $connection;
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
	public static function factory(\Db\Classes\Mysql\Connection $connection, $table_name)
	{
		$instance = new static($connection, $table_name);

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
	 * @return \Db\Result
	 */
	public function get_one($parts)
	{
		if (isset($parts)) {
			$this->select($parts);
		}
		$query = $this->get_database()->select($this->get_selects())->from($this->table_name);

		if (isset($this->filter))
		{
			$query->filter($this->filter);
		}
		return $query->execute()->current();
	}

	public function get_all($parts = NULL)
	{
		if (isset($parts)) {
			$this->select($parts);
		}
		$query = $this->get_database()->select($this->get_selects())->from($this);
		$joins = $this->get_joins();
		if(isset($joins)) {
			foreach ($joins as $join) {
				$query->join($join);
			}
		}
		$groups = $this->get_groups();
		if(isset($groups)) {
			foreach ($groups as $group) {
				$query->group($group);
			}
		}

		$orders = $this->get_orders();
		if(isset($orders)) {
			foreach ($orders as $order) {
				$query->order($order);
			}
		}

		if (isset($this->filter))
		{
			$query->filter($this->filter);
		}
		return $query->execute();
	}

	/**
	 * @return \Db\Interfaces\Connection
	 */
	public function get_database()
	{
		return $this->database;
	}

	/**
	 * @return \Db\Interfaces\Connection
	 */
	public function get_connection()
	{
		return $this->get_database();
	}

	/**
	 *
	 */
	public function get_result()
	{
		if ( ! isset($this->result)) {

		}
	}

	/**
	 * @return \Db\Classes\Table\Column[]
	 */
	public function get_columns()
	{
		if ($this->columns === NULL)
		{
			$db = $this->get_database();
			$this->columns = $db->describe_table($this)->map_to('\Db\Classes\Table\Column');

			foreach ($this->columns as $column)
			{
				$column->set_table($this);
			}
		}

		return $this->columns;
	}

	public function add_column($column)
	{

		$this->columns[$column->get_field()] = $column;
	}

	public function select($statement, $alias = NULL)
	{
		if ( ! isset($this->select))
		{
			$this->select = array();
		}

		if ($statement instanceof \Db\Classes\Table\Select)
		{
			$this->select[] = $statement;
		} else {
			$this->select[] = \Db\Classes\Table\Select::factory($statement, $alias);
		}

		return $this;
	}
	public function get_column($name)
	{
		$columns = $this->get_columns();
		foreach ($columns as $column)
		{
			if ($column->get_field() == $name)
			{
				return $column;
			}
		}

		return NULL;
	}
	public function get_table_name()
	{
		return $this->table_name;
	}

	/**
	 * @param Table $linked_table
	 * @param Filter $link
	 * @return $this
	 */
	public function join(\Db\Classes\Table $linked_table, \Db\Classes\Filter $link)
	{
		if ( ! isset($this->joined_tables))
		{
			$this->joined_tables = array();
		}
		$this->joined_tables[] = \Db\Classes\Table\Join::factory($this, $linked_table, $link);

		return $this;
	}

	public function group(\Db\Classes\Table\Column $column)
	{
		if ( ! isset($this->group))
		{
			$this->group = array();
		}

		$this->group[] = \Db\Classes\Table\Group::factory($column);

		return $this;
	}

	public function order($select, $direction = NULL)
	{
		if (( ! $select instanceof \Db\Classes\Table\Column) AND ( ! $select instanceof \Db\Classes\Table\Select))
		{
			throw new Exception('Select must be Table or Select Statement');
		}
		if ( ! isset($this->order))
		{
			$this->order = array();
		}

		$this->order[] = \Db\Classes\Table\Order::factory($select, $direction);

		return $this;
	}

	public function get_selects()
	{
		return $this->select;
	}

	public function get_joins()
	{
		return $this->joined_tables;
	}
	public function get_groups()
	{
		return $this->group;
	}

	public function get_orders()
	{
		return $this->order;
	}
	public final function before_sleep()
	{
		unset($this->database);
	}

	public function __clone()
	{

		$columns = $this->get_columns();

		if (isset($columns)) {
			$new_columns = clone $columns;
			foreach ($new_columns as $column) {
				$column->set_table($this);
			}
			$this->columns = $new_columns;
		}
		return $this;
	}
} 