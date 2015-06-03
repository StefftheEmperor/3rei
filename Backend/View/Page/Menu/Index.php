<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 31.05.15
 * Time: 16:47
 */

namespace Backend\View\Page\Menu;


class Index extends \Request\Classes\View {

	public function get_html()
	{
		$node = $this->get_menu()->get_node();
		$elem = 'Node '.$node->get_model()->get_id();
		if (isset($node->request_id)) {
			$node_request = \Request\Classes\Request::factory_by_id($node->get_request_id());
			$node_rewrite = \Request\Model\Rewrite::factory_by_request($node_request->get_model());
			$elem = \Structure\Classes\Link::factory();
			$elem->set_href($node_rewrite->get_url());
		}
		$children = $node->get_children();
		return $this->get_renderer()->render($elem);
	}
}