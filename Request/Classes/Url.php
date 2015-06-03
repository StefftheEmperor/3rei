<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 27.07.14
 * Time: 17:25
 */
namespace Request\Classes;
class Url
{
	const SCHEME_HTTP = 'http';
	const SCHEME_HTTPS = 'https';

	private $scheme = 'http';
	private $domain = null;
	private $url = null;
	private $params = array();

	/**
	 * @var \Request\Model\Rewrite|NULL $rewrite
	 */
	private $rewrite = NULL;

	public static function get_instance($url = null)
	{
		return new static($url);
	}

	public function __construct($url = null)
	{

		if ($url === null) {
			$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		}
		$url_components = parse_url($url);
		if (isset($url_components['scheme'])) {
			$this->scheme = $url_components['scheme'];
		} else {
			$this->scheme = static::SCHEME_HTTP;
		}

		if (isset($url_components['host'])) {
			$this->domain = $url_components['host'];
		} else {
			$this->domain = $_SERVER['SERVER_NAME'];
		}

		if (isset($url_components['path'])) {
			$this->url = ((substr($url_components['path'],0,1) === '/') ? substr($url_components['path'],1) : $url_components['path']);
		}

		if (isset($url_components['query'])) {
			parse_str($url_components['query'], $this->params);
		}

	}

	/**
	 * @param $params
	 * @return $this
	 */
	public function set_params($params)
	{
		$this->params = $params;

		return $this;
	}

	public function redirect()
	{
		$registry = Registry::get_instance();
		if (isset($registry['session'])) {
			$registry['session']->save();
		}
		header('Location: '.$this->get_absolute_url());
	}

	public function get_absolute_url()
	{
		return $this->scheme . '://'.$this->domain.($this->get_url()).(count($this->params) ? '?'.http_build_query($this->params) : '');
	}

	public function get_domain()
	{
		return $this->domain;
	}

	public function get_host()
	{
		return $this->get_domain();
	}

	public function get_url()
	{
		return '/'.$this->url;
	}
} 