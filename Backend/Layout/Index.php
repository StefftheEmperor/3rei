<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 03.05.15
 * Time: 18:38
 */

namespace Backend\Layout;

class Index extends \Template\Classes\Layout {

	public function get_html()
	{
		if (\Model\Classes\Registry::get_instance()->offsetExists('metadata')) {
			$metadata = \Model\Classes\Registry::get_instance()->get('metadata');
		} else {
			$metadata = new \Template\Classes\Metadata;
		}
		return '<html>
	<head>
		<title>'.$metadata->get_title().'</title>
	</head>
	<body class="backend">
		<div class="content_wrapper">
			<div class="menu">
			'.$this->get_menu().'
			</div>
			<div class="content">
				'.$this->get_content().'
			</div>
		</div>
	</body>
</html>';
	}
}