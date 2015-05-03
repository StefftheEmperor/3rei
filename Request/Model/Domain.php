<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 01.02.15
 * Time: 17:14
 */

namespace Request\Model;


class Domain extends \Db\Classes\AbstractModel implements \Db\Interfaces\Model {
	protected $primary_key = 'id';
	protected $table_name = 'request__domain';

}