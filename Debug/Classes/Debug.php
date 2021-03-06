<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 16.11.14
 * Time: 20:34
 */
namespace Debug\Classes;

class Debug {

	protected static $current_exception = NULL;
	public function exception_handler(\Throwable $incoming_exception)
	{

		if ($incoming_exception instanceof CustomException) {
			$exception = $incoming_exception;
		} else {
			$exception = new \Debug\Classes\CustomException($incoming_exception->getMessage(), $incoming_exception->getCode());
			$exception->set_file($incoming_exception->getFile());
			$exception->set_line($incoming_exception->getLine());
			$exception->set_trace($incoming_exception->getTrace());

		}
		if ( ! is_null(static::$current_exception)) {
			$exception->set_previous_exception(static::$current_exception);
		}

		static::$current_exception = $exception;

		return TRUE;
	}

	public function error_handler($code = 0, $message = "", $file, $line, $context)
	{
		$exception = new \Debug\Classes\CustomException($message, $code);
		$exception->set_file($file);
		$exception->set_line($line);
		$exception->set_context($context);

		throw $exception;
	}

	public function shutdown()
	{

		$current = static::$current_exception;
		echo $current;
	}
} 