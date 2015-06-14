<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 03.05.15
 * Time: 18:10
 */

namespace Template\Classes;

class Layout extends \Request\Classes\Layout {

	public function get_html()
	{
		return '<html>
	<head>
		<title>Your Project</title>
	</head>
	<body>
		<div class="content">
			'.$this->get_content().'
		</div>
	</body>
</html>';
	}
}