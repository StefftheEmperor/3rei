<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 03.05.15
 * Time: 18:44
 */

namespace Template\Classes;


class Metadata {

	protected $titles = array();
	protected $keywords = array();
	protected $description = '';
	protected $title_separator = ' - ';
	public function add_title($title)
	{
		$this->titles[] = $title;
	}

	public function add_keyword($keyword)
	{
		$this->keywords[] = $keyword;
	}

	public function set_description($description)
	{
		$this->description = $description;
	}

	public function get_titles()
	{
		return $this->titles;
	}

	public function get_title()
	{
		return implode($this->get_separator(), $this->get_titles());
	}

	public function get_keywords()
	{
		return $this->keywords;
	}

	public function get_description()
	{
		return $this->description;
	}

	public function get_separator()
	{
		return $this->title_separator;
	}
}