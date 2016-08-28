<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.08.16
 * Time: 17:55
 */

namespace Response\Classes\Header\Http;


use Header\Interfaces\Header;

class ContentType implements Header
{
	protected $content_type;

	public function send()
	{
		header('Content-Type: '.$this->content_type);
	}
}