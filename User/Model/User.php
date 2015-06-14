<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 09.09.14
 * Time: 18:12
 */

namespace Model;
class User extends \AbstractModel {

	private $permissions = NULL;

	public static function factory_by_facebook_user_id($fb_userid)
	{

		$filter = new \Db\Filter('fb_user_id', '=', $fb_userid);
		$result = \Db\Table::factory('user')->filter($filter)->get_one(\Db\Classes\Table\Select\All::factory());

		return $result->map_to(get_called_class());
	}

	public function load_permissions()
	{
		$this->permissions = Permission::factory_by_user_id($this->get_id());
	}

	public function get_permissions()
	{
		if ( ! isset($this->permissions))
		{
			$this->load_permissions();
		}

		return $this->permissions;
	}
	public function has_right($right)
	{
		$permissions = $this->get_permissions();

		return ($permissions->find($right) instanceof \Model\Permission);
	}
} 