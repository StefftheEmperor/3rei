<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 26.04.15
 * Time: 14:09
 */

namespace Db\Classes;

class ForeignKey {

	protected $key;
	protected $foreign_key;
	protected $foreign_model;

	public function __construct($key, $foreign_key, $foreign_model)
	{
		$this->key = $key;
		$this->foreign_key = $foreign_key;
		$this->foreign_model = $foreign_model;
	}

	public static function factory($key, $foreign_key, $foreign_model)
	{
		return new static($key, $foreign_key, $foreign_model);
	}


	public function get_key()
	{
		return $this->key;
	}

	public function get_foreign_key()
	{
		return $this->foreign_key;
	}

	public function get_foreign_model()
	{
		return $this->foreign_model;
	}

}