<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 12.06.15
 * Time: 10:24
 */

namespace Structure\Classes;


class Block extends \Structure\Classes\StructureList
{



	public function get_html()
	{
		$content = '<div'.$this->get_attributes_html().'>';

		foreach ($this->get_elements() as $element)
		{
			$content .= $this->get_renderer()->render($element);
		}

		$content .= '</div>';
		return $content;

	}
}