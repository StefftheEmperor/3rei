<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 03.08.14
 * Time: 18:02
 */

class ClassNamespace {
	private $namespace;
	private $directory;
	public function __construct($namespace, $directory)
	{
		$this->namespace = $namespace;
		$this->directory = $directory;
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

	public function getDirectory()
	{
		return $this->directory;
	}
} 