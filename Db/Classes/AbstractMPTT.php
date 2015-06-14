<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 17.05.15
 * Time: 15:50
 */

namespace Db\Classes;


abstract class AbstractMPTT extends \Model\Classes\AbstractMPTT
implements \Db\Interfaces\Model, \Db\Interfaces\MPTT

{
	use \Db\Traits\Model;


	public function get_left_boundary()
	{
		return $this->__get($this->get_left_boundary_identifier());
	}

	public function get_right_boundary()
	{
		return $this->__get($this->get_right_boundary_identifier());
	}

}