<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 18:50
 */

namespace Structure\Classes;


class StructureList extends \Structure\Classes\AbstractList {

	public function get_html()
	{
		$content = '';

		foreach ($this->get_elements() as $element)
		{
			$content .= $this->get_renderer()->render($element);
		}

		return $content;
	}
}