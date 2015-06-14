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

		$backend_root_request = \Request\Classes\Request::factory_by_url($this->get_controller()->get_database_connection(), \Request\Classes\Url::get_instance('/backend'));
		$menu_node = \Page\Classes\Menu\Node::factory_by_request($backend_root_request);

		$menu_request = $this->get_controller()->get_new_child_request();

		$menu_request->set_params(array('controller' => 'Page\Menu', 'view' => 'Index', 'action' => 'Index', 'menu_id' => $menu_node->get_id()));

		$menu = $menu_request->execute();

		return '<html>
	<head>
		<title>'.$metadata->get_title().'</title>
	</head>
	<body class="backend">
		<div class="content_wrapper">
			<div class="menu">
			'.$menu.'
			</div>
			<div class="content">
				'.$this->get_content().'
			</div>
		</div>
	</body>
</html>';
	}
}