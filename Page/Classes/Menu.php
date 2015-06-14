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
	protected $children = NULL;
	protected $parent = NULL;
	public static function factory(\Page\Classes\Menu\Node $node)
	{
		$menu = new static;
		$menu->set_node($node);

		return $menu;
	}

	/**
	 * @return \Page\Classes\Menu\Node
	 */
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

	public function load_children()
	{
		$child_nodes = $this->get_node()->get_children();

		$this->children = array();

		if (isset($child_nodes)) {
			foreach ($child_nodes as $child_node) {
				$child_menu = static::factory($child_node);

				$this->add_child($child_menu);
			}
		}
	}

	public function add_child(\Page\Classes\Menu $child_menu)
	{
		$this->children[] = $child_menu;
		$child_menu->set_parent($this);
	}

	/**
	 * @return \Page\Classes\Menu[]
	 */
	public function get_children()
	{
		if ( ! isset($this->children))
		{
			$this->load_children();
		}

		return $this->children;
	}

	public function set_parent(\Page\Classes\Menu $parent)
	{
		$this->parent = $parent;

		return $this;
	}

	public function get_parent()
	{
		return $this->parent;
	}

	public function get_id()
	{
		return $this->get_node()->get_id();
	}
}