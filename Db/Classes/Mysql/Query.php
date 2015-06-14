<?php
/**
 * Created by PhpStorm.
 * User: shireen
 * Date: 10.09.14
 * Time: 18:31
 */

namespace Db\Classes\Mysql;

use Db\Classes\AbstractQuery;

class Query extends AbstractQuery {


	protected $parts = array();

	/**
	 * @var \Db\Classes\Mysql\Connection
	 */
	protected $connection;

	protected $type;

	protected $from;

	protected $filter = array();

	protected $join = NULL;

	protected $group = NULL;

	protected $order = NULL;

	protected $scope;
	/**
	 * @param $connection
	 * @param $type
	 * @param $parts
	 */
	public function __construct($connection, $type, $parts = NULL)
	{
		$this->connection = $connection;
		$this->parts = $parts;
		$this->type = $type;
		$this->scope = new \Db\Classes\Mysql\Scope;
	}

	public function from($table) {
		if (is_string($table)) {
			$table = new \Db\Classes\Table($this->get_connection(), $table);
		}

		$this->from = $table;

		return $this;
	}

	public function filter(\Db\Classes\Filter $filter)
	{
		$this->filter = array($filter);

		return $this;
	}

	public function set_filter($filter)
	{
		return $this->filter($filter);
	}

	/**
	 * @return Statement
	 */
	public function execute()
	{
		$result = $this->connection->query($this);

		return $result;
	}

	/**
	 * @param $connection
	 * @param $parts
	 * @return $this
	 */
	public static function select($connection, $parts)
	{
		return new static($connection, static::QUERY_SELECT, $parts);
	}

	public function is_select()
	{
		return $this->type === static::QUERY_SELECT;
	}

	public function is_describe()
	{
		return $this->type === static::QUERY_DESCRIBE;
	}

	public function is_insert()
	{
		return $this->type === static::QUERY_INSERT;
	}

	public function is_update()
	{
		return $this->type === static::QUERY_UPDATE;
	}

	/**
	 * @return \Db\Classes\Table
	 */
	public function get_table()
	{
		return $this->get_from();
	}

	public function set_table($table)
	{
		return $this->from($table);
	}

	public function get_parts()
	{
		return $this->parts;
	}

	public function get_filter_string(\Db\Interfaces\Filter $filter, \Db\Classes\Mysql\Scope $scope)
	{
		$where = '';
		$operands = $filter->get_operands();
		$operand_strings = array();
		foreach ($operands as $operand_key => $operand)
		{
			if ($operand instanceof \Db\Classes\Expression\AbstractExpression)
			{
				$operand = $operand->map_to_namespace('\Db\Classes\Mysql\Expression');
				if ($operand instanceof \Db\Classes\Mysql\Expression\Row)
				{
					$operand_unfiltered = $operand->get_unfiltered();

					if (is_object($operand_unfiltered) AND $operand_unfiltered instanceof \Db\Classes\Table\Column)
					{
						$column = $operand_unfiltered;
						$field = $operand_unfiltered->get_field();
					} elseif (is_string($operand_unfiltered))
					{
						$column = $this->get_table()->get_column($operand->get_unfiltered());
						$field = $column->get_field();
					}

					if ($scope->alias_exists_for($column->get_table()))
					{
						$table = $scope->get_alias_for($column->get_table());
					} else {
						$table = '`'.$column->get_table()->get_table_name().'`';
					}
					if ($scope->alias_exists_for($column))
					{
						$operand = $scope->get_alias_for($column);
					} else {
						$operand = $table . '.`' . $field . '`';
					}
				}
				else
				{
					$operand = $operand->get_filtered();
				}
			} elseif ($operand instanceof \Db\Classes\Table\Column)
			{
				if ($scope->alias_exists_for($operand))
				{
					$operand = $scope->get_alias_for($operand);
				}
				else {
					$table = $operand->get_table();

					if ($scope->alias_exists_for($table))
					{
						$table = $scope->get_alias_for($table);
					} else {
						$table = '`'.$operand->get_table()->get_table_name().'`';
					}
					$operand = $table.'.`'.$operand->get_field().'`';
				}

			}

			$operand_strings[$operand_key] = $operand;
		}

		$type = get_class($filter);

		switch ($type)
		{
			case 'Db\Classes\Filter\Comparison':
				$where .= $operand_strings[0] . ' = '.$operand_strings[1];
				break;
			case 'Db\Classes\Filter\Regexp':
					$where .= $operand_strings[0] . ' REGEXP '.$operand_strings[1];
				break;
			case 'Db\Classes\Filter\Between':
					$where .= $operand_strings[0] . ' BETWEEN '.$operand_strings[1].' AND '.$operand_strings[2];
				break;

		}


		return $where;
	}

	public function get_select_string(\Db\Classes\Mysql\Scope $scope)
	{
		$select_string = '';
		$parts = $this->get_parts();
		if ( ! isset($parts))
		{
			throw new \Db\Classes\Exception('There are no parts selected in query');
		}

		if ( ! is_array($parts) AND ( ! $parts instanceof \Iterator))
		{
			$parts = array($parts);
		}

		$i = 0;
		foreach ($parts as $part) {
			if ($part instanceof \Db\Classes\Table\Select\All) {
				$table = $part->get_table();
				if ( ! isset($table))
				{
					$table = $this->get_table();
				}
				$columns = $table->get_columns();

				foreach ($columns as $column) {
					if ($i == 0) {
						$select_string .= '';
					} else {
						$select_string .= ', ';
					}

					$select_string .= $scope->get_alias_for($table) . '.`' . $column->get_field() . '` AS ' . $scope->get_alias_for($column);
					$i++;
				}
			} elseif ($part instanceof \Db\Classes\Table\Select)
			{
				if ($i == 0) {
					$select_string .= '';
				} else {
					$select_string .= ', ';
				}
				$select_string .= $part->get_statement().' AS '.$scope->get_alias_for($part);
				$i++;
			}
		}
		return $select_string;
	}

	public function get_from_string(\Db\Classes\Mysql\Scope $scope)
	{
		$from_string = '';
		$from = $this->get_from();

		if ( ! isset($from) OR ( ! $from instanceof \Db\Classes\Table))
		{
			throw new \Db\Classes\Exception('You need to define a table to select from');
		}

		$from_string .= ' FROM  `'.$this->get_connection()->get_database().'`.`'.$from->get_table_name().'` AS '.$scope->get_alias_for($from);

		return $from_string;
	}

	public function get_where_as_string(\Db\Classes\Mysql\Scope $scope)
	{
		$where = '';
		$filters = $this->get_filter();
		if (count($filters) > 0) {
			$filter_iterator = 0;
			foreach ($filters as $filter) {
				if ($filter_iterator == 0) {
					$where .= ' WHERE ';
				} elseif ($filter_iterator > 0) {
					$where .= ' AND ';
				}
				$where .= $this->get_filter_string($filter, $scope);
			}
		}

		return $where;
	}

	public function get_joins_as_string(\Db\Classes\Mysql\Scope $scope)
	{
		$joins_string = '';
		$joins = $this->get_joins();
		if (isset($joins))
		{
			$child_scope = $scope->get_new_child(array(\Db\Classes\Mysql\Scope::OPTION_INHERIT_TABLES));
			$i=0;
			foreach ($joins as $join)
			{
				$joins_string .= ' JOIN `'.$join->get_linked_table()->get_connection()->get_database().'`.`'.$join->get_linked_table()->get_table_name().'` AS '.$child_scope->get_alias_for($join->get_linked_table()).' ON '.$this->get_filter_string($join->get_link(), $child_scope);
				$i++;
			}
		}

		return $joins_string;
	}

	public function get_order_string(\Db\Classes\Mysql\Scope $scope)
	{
		$order_string = '';
		$orders = $this->get_orders();

		if (isset($orders))
		{
			$i=0;
			foreach ($orders as $order) {
				if ($i == 0) {
					$order_string .= ' ORDER BY ';
				} else {
					$order_string .= ', ';
				}
				$direction = '';
				if ($order->get_direction() === \Db\Classes\Table\Order::DIRECTION_ASCENDING)
				{
					$direction = ' ASC';
				} elseif ($order->get_direction() === \Db\Classes\Table\Order::DIRECTION_DESCENDING)
				{
					$direction = ' DESC';
				}
				if ($scope->alias_exists_for($order->get_select()))
				{
					$order = $scope->get_alias_for($order->get_select());
				} else {
					if ($order->get_select() instanceof \Db\Classes\Table\Column) {
						if ($scope->alias_exists_for($order->get_select()->get_table())) {
							$table = $scope->get_alias_for($order->get_select()->get_table());
						} else {
							$table = '`' . $order->get_select()->get_table()->get_table_name() . '`';
						}
						$order = $table . '.`' . $order->get_select()->get_field() . '`';
					} elseif ($order->get_select() instanceof \Db\Classes\Table\Select)
					{
						$order = $order->get_select()->get_statement();
					}
				}
				$order_string .= $order.$direction;
				$i++;
			}
		}

		return $order_string;
	}

	public function get_group_string(\Db\Classes\Mysql\Scope $scope)
	{
		$group_string = '';
		$groups = $this->get_groups();

		if (isset($groups))
		{
			$i=0;
			foreach ($groups as $group)
			{
				if ($i == 0)
				{
					$group_string .= ' GROUP BY ';
				} else {
					$group_string .= ', ';
				}

				if ($scope->alias_exists_for($group->get_column()))
				{
					$group = $scope->get_alias_for($group->get_column());
				} else {
					if ($scope->alias_exists_for($group->get_column()->get_table()))
					{
						$table = $scope->get_alias_for($group->get_column()->get_table());
					} else {
						$table = '`'.$group->get_column()->get_table()->get_table_name().'`';
					}

					$group = $table.'.`'.$group->get_column()->get_field().'`';
				}
				$group_string .= $group;;
				$i++;
			}
		}

		return $group_string;
	}
	public function get_filter()
	{
		return $this->filter;
	}

	public function get_type()
	{
		return $this->type;
	}

	public function get_from()
	{
		return $this->from;
	}

	public function set_from($from)
	{
		return $this->set_table($from);
	}

	public function get_connection()
	{
		return $this->connection;
	}

	/**
	 * @return \Db\Classes\Table\Join[]
	 */
	public function get_joins()
	{
		return $this->join;
	}

	public function join(\Db\Classes\Table\Join $join)
	{
		if ( ! isset($this->join))
		{
			$this->join = array();
		}

		$this->join[] = $join;

		return $this;
	}

	/**
	 * @return \Db\Classes\Table\Group[]
	 */
	public function get_groups()
	{
		return $this->group;
	}

	public function group(\Db\Classes\Table\Group $group)
	{
		if ( ! isset($this->group))
		{
			$this->group = array();
		}

		$this->group[] = $group;

		return $this;
	}

	/**
	 * @return \Db\Classes\Table\Order[]
	 */
	public function get_orders()
	{
		return $this->order;
	}

	public function order(\Db\Classes\Table\Order $order)
	{
		if ( ! isset($this->join))
		{
			$this->join = array();
		}

		$this->order[] = $order;

		return $this;
	}

	/**
	 * @param string $alias
	 * @return string
	 * @throws \Db\Classes\Exception
	 */
	public function get_property_name_by_alias($alias)
	{
		if ($this->is_describe()) {
			return $alias;
		}

		return $this->get_scope()->get_property_name_by_alias($alias);
	}

	/**
	 * @return \Db\Classes\Mysql\Scope
	 */
	public function get_scope()
	{
		return $this->scope;
	}
}