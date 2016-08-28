<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 31.07.16
 * Time: 12:12
 */

namespace Config\Model\Config;

use Db\Classes\AbstractMPTT;

class Node extends AbstractMPTT
{
	protected $left_boundary_identifier= 'lbd';
	protected $right_boundary_identifier = 'rbd';

	public function get_left_boundary_identifier()
	{
		return $this->left_boundary_identifier;
	}

	public function get_right_boundary_identifier()
	{
		return $this->right_boundary_identifier;
	}

	public function get_primary_key()
	{
		return 'id';
	}

	public function get_table_name()
	{
		return 'config__node';
	}

}