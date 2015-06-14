<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 12.06.15
 * Time: 08:48
 */

namespace DB\Interfaces;


interface MPTT {


	public function get_left_boundary_identifier();

	public function get_right_boundary_identifier();

}