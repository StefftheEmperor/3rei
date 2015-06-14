<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 12.06.15
 * Time: 11:16
 */

namespace Structure\Interfaces;


use Request\Classes\Request\Post;

interface Value {

	public function validate(Post $post_data);
	public function get_value();
	public function set_value($value);
	public function get_key();
	public function set_key($key);

	public function get_value_of($key);

}