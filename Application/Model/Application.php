<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 01.02.15
 * Time: 17:15
 */

namespace Application\Model;


class Application extends \Db\Classes\AbstractModel implements \Db\Interfaces\Model {
	protected $table_name = 'application';
	protected $primary_key = 'id';
}