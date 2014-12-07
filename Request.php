<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 16.11.14
 * Time: 17:27
 */

class Request {

	protected $url = NULL;
	protected $rewrite = NULL;

	public static function factory_by_url(\Url $url)
	{
		$request = new static;
		$request->set_url($url);

		return $request;
	}

	public function set_url(\Url $url)
	{
		$this->url = $url;
	}
} 