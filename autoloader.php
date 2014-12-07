<?php
	require_once 'ClassNamespace.php';
	class Autoloader {
		private $namespaces = array();

		public function __construct() {
			$this->namespaces[] = new \ClassNamespace('\\', __DIR__);
		}
		public function add_namespace($namespace, $location) {
			$this->namespaces[] = new \ClassNamespace($namespace, $location);
		}

		public function autoload($classname)
		{
			$segments = explode('\\', $classname);
			$class = array_pop($segments);
			$namespace = implode('\\', $segments);
			if (substr($namespace, 0, 1) !== '\\') {
				$namespace = __NAMESPACE__ . $namespace;
			}

			if (substr($namespace, 0, 1) !== '\\') {
				$namespace = '\\'.$namespace;
			}

			if (empty($namespace)) {
				$namespace = '\\';
			}

			$found = FALSE;
			$unshift = array();

			do {
				$segments = explode('\\', $namespace);
				$namespace = implode('\\', $segments);

				if (substr($namespace, 0, 1) !== '\\') {
					$namespace = '\\'.$namespace;
				}

				foreach ($this->namespaces as $sub_namespace) {
					if ($sub_namespace->getNamespace() === $namespace) {
						$sub_dir = implode(DIRECTORY_SEPARATOR, $unshift);
						$sub_namespace_directory =  $sub_namespace->getDirectory();
						if (substr($sub_namespace_directory, -1) !== DIRECTORY_SEPARATOR) {
							$sub_namespace_directory = $sub_namespace_directory . DIRECTORY_SEPARATOR;
						}
						$pathname = $sub_namespace_directory.(!empty($sub_dir) ? ($sub_dir . DIRECTORY_SEPARATOR) : '');
						if (substr($pathname, -1) !== DIRECTORY_SEPARATOR) {
							$pathname = $pathname . DIRECTORY_SEPARATOR;
						}
						$filename = $pathname.$class.'.php';

						if (!file_exists($filename)) {
							$filename = $pathname . strtolower($classname).'.php';
						}
						if (file_exists($filename)) {
							include_once($filename);
							$found = TRUE;
							break 2;
						}
					}
				}
				array_unshift($unshift, array_pop($segments));

				$namespace = implode('\\', $segments);
			} while (count($segments) > 0);

			return $found;
		}
	}