<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 27.07.14
 * Time: 17:25
 */
namespace Request\Classes;
class Url {

	private $scheme = 'http';
	private $domain = null;
	private $url = '';
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
		}

		if (isset($url_components['host'])) {
			$this->domain = $url_components['host'];
		}

		if (isset($url_components['path'])) {
			$this->url = ((substr($url_components['path'],0,1) === '/') ? substr($url_components['path'],1) : $url_components['path']);
		}

		if (isset($url_components['query'])) {
			parse_str($url_components['query'], $this->params);
		}

		if ( ! isset($this->rewrite))
		{
			$this->set_rewrite(\Request\Model\Rewrite::factory_by_url($this));
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

	/**
	 * @return \Request\Model\Rewrite
	 */
	public function get_rewrite()
	{
		if ( ! isset($this->rewrite))
		{
			$this->rewrite = new \Request\Model\Rewrite;
			$this->rewrite->set_url($this);
		}

		return $this->rewrite;
	}

	/**
	 * @param \Request\Model\Rewrite $rewrite
	 * @return $this
	 */
	public function set_rewrite(\Request\Model\Rewrite $rewrite)
	{
		$this->rewrite = $rewrite;

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
		return $this->scheme . '://'.$this->domain.($this->url ? '/' . $this->url : '').(count($this->params) ? '?'.http_build_query($this->params) : '');
	}

} 