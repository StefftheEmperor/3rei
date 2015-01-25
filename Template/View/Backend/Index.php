<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 25.01.15
 * Time: 19:13
 */

namespace Template\View\Backend;


class Index extends \Request\Classes\View {

	public function get_html()
	{
		$content = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
<title>Backend</title>
</head>
<body>
<div>Content</div>
</body>
</html>
HTML;

		return $content;
	}
}