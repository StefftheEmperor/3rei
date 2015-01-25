<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 27.07.14
 * Time: 17:42
 */
namespace Session\Classes;
class Session extends \Model\Classes\AbstractModel {

	public function __construct()
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();

			$this->offsetSet('session_id', session_id());
			foreach ($_SESSION as $key => $value) {
				$this->offsetSet($key, $value);
			}
		}
	}

	public function __destruct()
	{
		$this->save();
	}

	public function save()
	{
		foreach ($this as $key => $value) {
			$_SESSION[$key] = $value;
		}
	}
} 