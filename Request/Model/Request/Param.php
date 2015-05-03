<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 01.02.15
 * Time: 17:28
 */

namespace Request\Model\Request;


class Param extends \Db\Classes\AbstractModel implements \Db\Interfaces\Model {
	protected $primary_key = 'id';
	protected $table_name = 'request__param';

	public function get_table_name()
	{
		return $this->table_name;
	}
}