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
		/**
		 * @var \Page\Classes\Menu\Node $node
		 */
		$node = $this->get_menu()->get_node();
		$elem = 'Node '.$node->get_model()->get_id();
		$modificators = \Structure\Classes\Block::factory();
		if (isset($node->get_model()->request_id)) {
			$node_request = \Request\Classes\Request::factory_by_id($this->get_controller()->get_database_connection(), $node->get_model()->get_request_id());
			$node_rewrite = \Request\Model\Rewrite::factory_by_request($this->get_controller()->get_database_connection(), $node_request->get_model());
			if (isset($node_rewrite->url)) {
				$elem = \Structure\Classes\Link::factory('link', $node_rewrite->get_url());
				$elem->set_href($node_rewrite->get_url());
			}
		}

		$modificators->add(\Structure\Classes\Link::factory('edit', 'edit')->set_href('/backend/page/menu/edit/'.$node->get_id()));
		$modificators->add(\Structure\Classes\Link::factory('add', 'add')->set_href('/backend/page/menu/add/'.$node->get_id()));
		$modificators->add(\Structure\Classes\Link::factory('remove', 'remove')->set_href('/backend/page/menu/remove/'.$node->get_id()));

		$children = $this->get_children();
		$children_html = '';
		if (count($children) > 0)
		{
			$children_html .= '<div class="children">';
			foreach ($children as $child) {
				$children_html .= $child;
			}
			$children_html .= '</div>';
		}
		return '<ul class="menu"><li class="node"><div class="node">'.$this->get_renderer()->render($elem).$this->get_renderer()->render($modificators).'</div>'.$children_html.'</li></ul>';
	}
}