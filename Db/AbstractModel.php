<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 30.11.14
 * Time: 17:07
 */

namespace Db;


use Db\Mysql\Query;

class AbstractModel extends \AbstractModel {

	private $table = NULL;
	protected $table_name = NULL;
	protected $primary_key = NULL;
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
	public function save()
	{
		$filter = new Filter($this->get_table()->get_primary_key(), '=', $this->__get($this->get_table()->get_primary_key()));
		if ($this->is_new()) {
			$query = new Query(\Registry::get('database'), Query::QUERY_INSERT, $this);
		} else {
			$query = new Query(\Registry::get('database'), Query::QUERY_UPDATE, $this);
		}

		$query->set_table($this->get_table())->set_filter($filter);

		$query->execute();
	}
} 