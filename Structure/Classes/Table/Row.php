<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 16:58
 */

namespace Structure\Classes\Table;


class Row implements \Request\Interfaces\Renderable {

	protected $cells = array();

	protected $renderer = NULL;
	public function get_renderer()
	{
		return $this->renderer;
	}

	public function set_renderer($renderer)
	{
		$this->renderer = $renderer;
		return $this;
	}

	public static function factory()
	{
		return new static;
	}

	public function add_cell(\Structure\Classes\Table\Row\Cell $cell)
	{
		$this->cells[] = $cell;

		return $this;
	}


	public function get_html()
	{
		$row = '<tr>';
		foreach ($this->cells as $cell)
		{
			$cell->set_renderer($this->get_renderer());
			$row .= $this->get_renderer()->render($cell);
		}
		$row .= '</tr>';
		return $row;
	}
}