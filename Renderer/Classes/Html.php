<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 25.01.15
 * Time: 18:55
 */

namespace Renderer\Classes;


class Html extends \Renderer\Classes\AbstractRenderer {

	public function render($view)
	{
		return $view->get_html();
	}
}