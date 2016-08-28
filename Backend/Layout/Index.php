<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 03.05.15
 * Time: 18:38
 */

namespace Backend\Layout;

use Model\Classes\Registry;
use Page\Classes\Menu\Node;
use Request\Classes\Request;
use Request\Classes\Rewrite\Params;
use Request\Classes\Url;
use Template\Classes\Layout;
use Template\Classes\Metadata;

class Index extends Layout {

	public function get_html()
	{
		if (Registry::get_instance()->offsetExists('metadata')) {
			$metadata = Registry::get_instance()->get('metadata');
		} else {
			$metadata = new Metadata();
		}

		$backend_root_request = Request::factory_by_url($this->get_controller()->get_database_connection(), Url::get_instance('/backend'));
		$menu_node = Node::factory_by_request($backend_root_request);

		$menu_request = $this->get_controller()->get_new_child_request();

		$menu_request->set_params(
			Params::factory(
				array(
					'controller' => 'Page\Menu',
					'view' => 'Index',
					'action' => 'Index',
					'menu_id' => $menu_node->get_id())));

		$menu_request->set_attribute('layout', 'Plain');

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