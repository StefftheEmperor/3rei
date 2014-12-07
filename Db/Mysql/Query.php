<?php
/**
 * Created by PhpStorm.
 * User: shireen
 * Date: 10.09.14
 * Time: 18:31
 */

namespace Db\Mysql;


class Query {

    const QUERY_SELECT = 'SELECT';
	const QUERY_DESCRIBE = 'DESCRIBE';
    protected $parts = array();

	/**
	 * @var \Db\Mysql\Connection
	 */
    protected $connection;

    protected $type;

    protected $from;

    protected $filter = array();

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
    }

    public function from($table) {
        if (is_string($table)) {
            $table = new \Db\Table($table);
        }

        $this->from = $table;

        return $this;
    }

    public function filter($filter)
    {
        $this->filter = array($filter);

        return $this;
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


}