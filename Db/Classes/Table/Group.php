<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 10.06.15
 * Time: 10:30
 */

namespace Db\Classes\Table;


class Group {
	protected $column;

	public static function factory(\Db\Classes\Table\Column $column)
	{
		$group = new static;

		$group->set_column($column);

		return $group;
	}

	public function set_column($column)
	{
		$this->column = $column;

		return $this;
	}

	public function get_column()
	{
		return $this->column;
	}
}