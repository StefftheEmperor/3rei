<?php
	require_once 'ClassNamespace.php';
	class Autoloader {
		private $namespaces = array();

		public function __construct() {
			$this->add_namespace('\\', __DIR__);
			$document_root = $_SERVER['DOCUMENT_ROOT'];
			$include_path_string = ini_get('include_path');
			$include_paths = explode(PATH_SEPARATOR, $include_path_string);

			foreach ($include_paths as $include_path)
			{
				if (substr($include_path,0,1) === '.')
				{
					$include_path = $document_root.DIRECTORY_SEPARATOR.$include_path;
				}
				$this->add_namespace('\\', $include_path);
			}
		}
		public function add_namespace($namespace, $location) {

			if (substr($location, -1) !== DIRECTORY_SEPARATOR)
			{
				$location = $location . DIRECTORY_SEPARATOR;
			}
			$this->namespaces[] = new \ClassNamespace($namespace, $location);

			if (file_exists($location.'autoload.php'))
			{
				include_once $location.'autoload.php';
			}
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

						if ( ! file_exists($filename)) {
							$filename = $pathname . strtolower($classname).'.php';
						}
						if (file_exists($filename)) {
							include_once($filename);
							$found = TRUE;
							break 2;
						}
						else
						{
							if (file_exists($pathname.'autoload.php'))
							{
								include_once $pathname.'autoload.php';
							}
						}
					}
				}
				array_unshift($unshift, array_pop($segments));

				$namespace = implode('\\', $segments);
			} while (count($segments) > 0);

			return $found;
		}
	}