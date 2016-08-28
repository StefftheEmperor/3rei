<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.08.16
 * Time: 17:51
 */

namespace Response\Classes;


abstract class AbstractResponse
{
	protected $headers;
	protected $body;

	public function __construct()
	{
		$this->headers = new Headers;
	}
}