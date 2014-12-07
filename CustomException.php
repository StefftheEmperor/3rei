<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 16.11.14
 * Time: 20:40
 */

class CustomException extends Exception {

	const ERROR_FILE_DELTA = 4;
	protected $context = array();

	protected $custom_trace;
	protected $previous = NULL;
	public function __construct($message = "", $code = 0, $previous = NULL)
	{
		parent::__construct($message, $code, $previous);
	}

	public function get_line()
	{
		return $this->getLine();
	}
	public function set_line($line)
	{
		$this->line = $line;

		return $this;
	}

	public function get_file()
	{
		return $this->getFile();
	}

	public function set_file($file)
	{
		$this->file = $file;

		return $this;
	}

	public function set_context($context)
	{
		$this->context = $context;

		return $this;
	}

	public function get_context()
	{
		return $this->context;
	}

	public function get_message()
	{
		return $this->getMessage();
	}

	public function set_trace($trace)
	{
		$this->custom_trace = $trace;

		return $this;
	}

	public function get_trace()
	{
		return $this->custom_trace;
	}


	public function set_previous_exception(\Exception $previous)
	{
		$this->previous = $previous;

		return $this;
	}

	public function get_previous()
	{
		return $this->getPrevious();
	}

	public function __toString()
	{


		$stack = $this->get_trace();

		$stack_string = '';
		$stack_string .= '<table>';

		$stack_string .= '<thead><tr><th>function</th><th>class</th><th>args</th></tr></thead><tbody>';
		$stack_item = reset($stack);

		if (
			$stack_item !== FALSE
			AND isset($stack_item['class'])
			AND isset($stack_item['type'])
			AND isset($stack_item['function'])
			AND ($stack_item['class'] == 'Debug')
			AND ($stack_item['type'] == '->')
			AND ($stack_item['function'] == 'exception_handler')
			AND isset($stack_item['args'][0])
			AND $stack_item['args'][0] instanceof CustomException)
		{

			$stack_item = $stack_item['args'][0];

			$stack = $stack_item->getTrace();
		}

		foreach ($stack as $stack_item) {

			$stack_string .= '<tr>';
			if (isset($stack_item['function'])) {
				$stack_string .= '<td>' . $stack_item['function'] . '</td>';
			} else {
				$stack_string .= '<td></td>';
			}
			if (isset($stack_item['class'])) {
				$stack_string .= '<td>' . $stack_item['class'] . '</td>';
			} else {
				$stack_string .= '<td></td>';
			}

			if (isset($stack_item['file']) AND isset($stack_item['line'])) {
				$file_delta = array();

				$lines_before = static::ERROR_FILE_DELTA;

				if (($file_delta_handle = fopen($stack_item['file'], 'r'))) {
					$start = $stack_item['line'] - 4;
					if ($start < 0) {
						$lines_before = $lines_before + $start;
						$start = 0;
					}
					for ($i = 0; $i < $start; $i++) {
						fgets($file_delta_handle);
					}
					for ($i = 0; $i < $lines_before; $i++) {

						$file_delta[] = array('line' => $start + $i, 'content' => fgets($file_delta_handle));
					}
					$file_delta[] = array('line' => $start + $lines_before + 1, 'content' => fgets($file_delta_handle), 'css' => 'active');
					for ($i = 0; $i < static::ERROR_FILE_DELTA; $i++) {
						if (feof($file_delta_handle)) {
							break;
						}
						$file_delta[] = array('line' => $start + $i, 'content' => fgets($file_delta_handle));

					}
				}
				if (!empty($file_delta)) {
					$stack_string .= '<tr><td><table>';
					foreach ($file_delta as $file_delta_line) {
						$stack_string .= '<tr' . (isset($file_delta_line['css']) ? (' class="' . $file_delta_line['css'] . '"') : '') . '>';
						$stack_string .= '<td class="line">' . $file_delta_line['line'] . '</td><td class="content"><pre>' . $file_delta_line['content'] . '</pre></td>';
						$stack_string .= '</tr>';
					}
					$stack_string .= '</table></td></tr>';
				}
			}
			$stack_string .= '</tr>';

				if (isset($stack_item['trace']) AND !empty($stack_item['trace'])) {
					foreach ($stack_item['trace'] as $trace_item) {
						$file_delta = array();

						$lines_before = static::ERROR_FILE_DELTA;

						if (isset($trace_item['file']) AND isset($trace_item['line']) AND ($file_delta_handle = fopen($trace_item['file']))) {
							$start = $trace_item['line'] - 4;
							if ($start < 0) {
								$lines_before = $lines_before + $start;
								$start = 0;
							}
							for ($i = 0; $i < $start; $i++) {
								fgets($file_delta_handle);
							}
							for ($i = 0; $i < $lines_before; $i++) {

								$file_delta[] = array('line' => $start + $i, 'content' => fgets($file_delta_handle));
							}
							$file_delta[] = array('line' => $start + $lines_before + 1, 'content' => fgets($file_delta_handle), 'css' => 'active');
							for ($i = 0; $i < static::ERROR_FILE_DELTA; $i++) {
								if (feof($file_delta_handle)) {
									break;
								}
								$file_delta[] = array('line' => $start + $i, 'content' => fgets($file_delta_handle));

							}
						}
						if (!empty($file_delta)) {
							$stack_string .= '<tr><td><table>';
							foreach ($file_delta as $file_delta_line) {
								$stack_string .= '<tr' . (isset($file_delta_line['css']) ? (' class="' . $file_delta_line['css'] . '"') : '') . '>';
								$stack_string .= '<td class="line">' . $file_delta_line['line'] . '</td><td>' . $file_delta_line['content'] . '</td>';
								$stack_string .= '</tr>';
							}
							$stack_string .= '</table></td></tr>';
						}
					}
				}

		}
		$stack_string .= '</tbody></table>';

		//return '';
		return '['.get_class($this).'] '.$this->get_message().'<br />'.$this->get_file().':'.$this->get_line().'<br>'.$this->get_previous().'<br> Stack: <div>'.$stack_string.'</div>';
	}
} 