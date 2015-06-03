<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 16:58
 */

namespace Structure\Classes;


class Table extends \Structure\Classes\AbstractStructure implements \Request\Interfaces\Renderable {

	protected $rows = array();

	public function add_row(\Structure\Classes\Table\Row $row)
	{
		$this->rows[] = $row;

		return $this;
	}

	public function get_html()
	{
		$table = '<table>';

		foreach ($this->rows as $row)
		{
			$row->set_renderer($this->get_renderer());
			$table .= $this->get_renderer()->render($row);
		}
		$table .= '</table>';
		return $table;
	}
}