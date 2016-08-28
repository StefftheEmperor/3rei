<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 25.01.15
 * Time: 18:55
 */

namespace Renderer\Classes;

use Renderer\Classes\AbstractRenderer;
use Renderer\Classes\Exception;

class Html extends AbstractRenderer {

	public function render($view)
	{
		if (is_string($view))
		{
			return $view;
		}
		else
		{
			$view->set_renderer($this);
			if (method_exists($view, 'get_html')) {
				return $view->get_html();
			} else {
				throw new Exception('View '.get_class($view).' does not support '.get_called_class().' as renderer');
			}
		}
	}
}