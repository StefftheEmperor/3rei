<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.08.16
 * Time: 15:30
 */


namespace Page\Classes;
namespace Page\Classes\Menu;
namespace Renderer\Interfaces\View;
namespace Request\Classes;
namespace Page\View\Page\Menu;

use Request\Classes\View;
use Renderer\Interfaces\View\Html;
/**
 * Class Index
 * @package Page\View\Page\Menu
 */
class Index extends View implements Html
{

	public function get_html()
	{
		/**
		 * @var \Page\Classes\Menu\Node $node
		 */
		$node = $this->get_menu()->get_node();
		$elem = 'Node '.$node->get_model()->get_id();

		if (isset($node->get_model()->request_id)) {
			$node_request = \Request\Classes\Request::factory_by_id($this->get_controller()->get_database_connection(), $node->get_model()->get_request_id());
			$node_rewrite = \Request\Model\Rewrite::factory_by_request($this->get_controller()->get_database_connection(), $node_request->get_model());
			if (isset($node_rewrite->url)) {
				$elem = \Structure\Classes\Link::factory('link', $node_rewrite->get_url());
				$elem->set_href($node_rewrite->get_url());
			}
		}

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
		return '<ul class="menu"><li class="node"><div class="node">'.$this->get_renderer()->render($elem).'</div>'.$children_html.'</li></ul>';
	}

}