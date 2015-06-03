<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 13:27
 */

namespace Mandator\Classes\Mysql;


class Connection extends \Db\Classes\Mysql\Connection {
	protected $mandator_application_instance = NULL;
	public function __construct(\Mandator\Model\Application\Instance $mandator_application_instance, $host, $port, $database, $username, $password)
	{
		parent::__construct($host, $port, $database, $username, $password);

		$this->mandator_application_instance = $mandator_application_instance;
	}
}