<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 10.06.15
 * Time: 09:57
 */

namespace Db\Classes\Table;


class Join {
	protected $original_table = NULL;
	protected $linked_table = NULL;
	protected $link = NULL;

	public static function factory(\Db\Classes\Table $original_table, \Db\Classes\Table $linked_table, \Db\Classes\Filter $link)
	{
		$join = new static;
		$join->set_original_table($original_table);
		$join->set_linked_table($linked_table);
		$join->set_link($link);

		return $join;
	}

	/**
	 * @return \Db\Classes\Table
	 */
	public function get_original_table()
	{
		return $this->original_table;
	}

	public function set_original_table(\Db\Classes\Table $original_table)
	{
		$this->original_table;

		return $this;
	}

	/**
	 * @return \Db\Classes\Table
	 */
	public function get_linked_table()
	{
		return $this->linked_table;
	}

	public function set_linked_table(\Db\Classes\Table $linked_table)
	{
		$this->linked_table = $linked_table;

		return $this;
	}

	public function get_link()
	{
		return $this->link;
	}

	public function set_link($link)
	{
		$this->link = $link;

		return $this;
	}
}