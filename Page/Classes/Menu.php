<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 17.05.15
 * Time: 15:42
 */

namespace Page\Classes;

class Menu
{

	protected $node = NULL;

	public static function factory(\Page\Classes\Menu\Node $node)
	{
		$menu = new static;
		$menu->set_node($node);

		return $menu;
	}

	public function get_node()
	{
		if ( ! isset($this->node))
		{
			$this->node = new \Page\Classes\Menu\Node;
		}

		return $this->node;
	}

	public function set_node(\Page\Classes\Menu\Node $node)
	{
		$this->node = $node;

		return $this;
	}


}