<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 25.01.15
 * Time: 21:52
 */

namespace Error\View\Error;


class Error_404 extends \Request\Classes\View {

	public function get_html()
	{
		return <<<'HTML'
<h1>Seite nicht gefunden</h1>

HTML;

	}
}