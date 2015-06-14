<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 11.06.15
 * Time: 10:17
 */

namespace Db\Classes\Mysql;


class Scope {

	const OPTION_INHERIT_TABLES = 'OPTION_INHERIT_TABLES';
	protected $alias = NULL;

	protected $parent = NULL;
	protected $children = NULL;

	public function get_alias_for($element)
	{
		if ( ! isset($this->alias))
		{
			$this->alias = array();
		}

		$alias = array_search($element, $this->alias, TRUE);

		if ( $alias === FALSE) {
			if (isset($this->children)) {
				foreach ($this->get_children() as $scope) {
					if ($scope->alias_exists_for($element)) {
						$alias = $scope->get_alias_for($element);
					}
				}
			}
		}

		if ($alias === FALSE)
		{
			$chars = array_merge(range('a','z'), range('A','Z'));

			do {
				$alias = '';
				for ($i=0;$i<8;$i++)
				{
					$alias .= $chars[rand(0,count($chars)-1)];
				}
			} while (array_key_exists($alias, $this->alias));

			$this->alias[$alias] = $element;
		}

		return $alias;
	}

	public function alias_exists_for($element)
	{
		if ( ! isset($this->alias) AND ! isset($this->children))
		{
			return FALSE;
		}

		$exists = FALSE;
		if (isset($this->alias))
		{
			$exists = in_array($element, $this->alias, TRUE);
		}

		if ( ! $exists AND isset($this->children))
		{
			foreach ($this->get_children() as $scope)
			{
				if ($scope->alias_exists_for($element))
				{
					$exists = TRUE;
					break;
				}
			}
		}
		return $exists;
	}

	public function alias_exists($alias)
	{
		if (array_key_exists($alias, $this->alias))
		{
			return TRUE;
		}

		if (isset($this->children))
		{
			foreach ($this->children as $scope)
			{
				if ($scope->alias_exists($alias))
				{
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	public function get_property_name_by_alias($alias)
	{
		if ($this->alias_exists($alias)) {
			$property = $this->alias[$alias];

			if ($property instanceof \Db\Classes\Table\Column) {
				return $property->get_field();
			} elseif ($property instanceof \Db\Classes\Table\Select) {
				return $property->get_alias();
			}
		} else {
			throw new \Db\Classes\Exception('Property ' . $alias . ' is not registered');
			return NULL;
		}
	}

	public function set_alias($alias, $element)
	{
		if ( ! isset($this->alias))
		{
			$this->alias = array();
		}

		if ($this->alias_exists_for($element) OR $this->alias_exists($alias))
		{
			throw new \Db\Classes\Exception('Alias collision - already exists');
		}

		$this->alias[$alias] = $element;

		return $this;
	}

	public function get_new_child($options = NULL)
	{
		$scope = new static;
		$scope->set_parent($this);

		if (isset($options) AND is_array($options))
		{
			if (in_array(static::OPTION_INHERIT_TABLES, $options))
			{
				foreach ($this->alias as $alias => $element)
				{
					if ($element instanceof \Db\Classes\Table) {
						$scope->set_alias($alias, $element);
					}
				}
			}
		}
		if ( ! isset($this->children))
		{
			$this->children = array();
		}

		$this->children[] = $scope;

		return $scope;
	}

	public function add_child(\Db\Classes\Mysql\Scope $scope)
	{
		$scope = clone $scope;

		if ( ! isset($this->children))
		{
			$this->children = array();
		}

		$this->children[] = $scope;

		$scope->set_parent($this);
	}

	public function get_children()
	{
		return $this->children;
	}

	public function get_parent()
	{
		return $this->parent;
	}

	public function set_parent(\Db\Classes\Mysql\Scope $scope)
	{
		$this->parent = $scope;

		return $this;
	}
}