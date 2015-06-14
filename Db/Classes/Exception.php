<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 29.11.14
 * Time: 21:15
 */

namespace Db\Classes;


class Exception extends \Debug\Classes\CustomException {

	protected $history = array();

	public function set_history($history)
	{
		$this->history = $history;

		return $this;
	}

	public function get_history()
	{
		return $this->history;
	}

} 