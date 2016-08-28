<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 17:47
 */

namespace Backend\Controller\Request;

use Debug\Classes\CustomException;
use \Request\Model as Request_Model;
use \Structure\Classes as Structure;
use \Backend\Classes as Backend_Classes;

class Params extends Backend_Classes\AbstractController
{

	public function action_edit()
	{
		$controller_request = $this->get_request();

		$request_id = NULL;
		if ($controller_request->has_attribute('request_id'))
		{
			$request_id = $controller_request->get_attribute('request_id')->get_value();
			$request = Request_Model\Request::factory_by_id($this->get_registry()->cloud_database, $request_id);
		}
		else
		{
			throw new CustomException('No Request found to set params for');
		}
		$form = Structure\Form::factory('request_params');
		$form->set_method(Structure\Form::METHOD_POST);
		$form->set_action($this->get_request()->get_url());

		$form->validate($this->get_request()->get_post_data());

		$list = Structure\StructureList::factory();
		foreach ($request->get_params() as $param)
		{
			$list->add(Structure\KeyValuePair::factory(Structure\Form\Input::factory('key['.$param->get_id().']', $param->get_key()), \Structure\Classes\Form\Input::factory('value['.$param->get_id().']',$param->get_value())));
		}

		if ($form->is_submitted($this->get_request()->get_post_data()))
		{
			$param = Request_Model\Request\Param::factory_by_key_value();
		}
		$list->add(Structure\KeyValuePair::factory(Structure\Form\Input::factory('key[]'), \Structure\Classes\Form\Input::factory('value[]')));

		$params = Structure\KeyValuePair::factory(Structure\Label::factory('Parameter'), $list);

		$form->add($params);
		$form->add(Structure\Form\Input\Submit::factory('save', 'save'));
		$this->get_view()->form = $form;
	}
}