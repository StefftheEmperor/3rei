<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 11.06.15
 * Time: 16:00
 */

namespace Backend\View\Page\Menu;


class Children extends \Request\Classes\View {

	public function get_html()
	{
		$html = '<ul>';
		foreach ($this->get_children() as $child)
		{
			$html .= '<li>'.$child.'</li>';
		}
		$html .= '</ul>';
		return $html;
	}
}