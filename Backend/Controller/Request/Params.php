<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 17:47
 */

namespace Backend\Controller\Request;


class Params extends \Backend\Classes\AbstractController
{

	public function action_edit()
	{
		$request_id = $this->get_request()->get_attribute('request_id');
		$request = \Request\Model\Request::factory_by_id($this->get_registry()->cloud_database, $request_id);

		$form = \Structure\Classes\Form::factory('request_params');
		$form->set_method(\Structure\Classes\Form::METHOD_POST);
		$form->set_action($this->get_request()->get_url());

		$list = \Structure\Classes\StructureList::factory();
		foreach ($request->get_params() as $param)
		{
			$list->add(\Structure\Classes\KeyValuePair::factory(\Structure\Classes\Form\Input::factory('key['.$param->get_id().']', $param->get_key()), \Structure\Classes\Form\Input::factory('value['.$param->get_id().']',$param->get_value())));
		}
		$list->add(\Structure\Classes\KeyValuePair::factory(\Structure\Classes\Form\Input::factory('key[]'), \Structure\Classes\Form\Input::factory('value[]')));

		$params = \Structure\Classes\KeyValuePair::factory(\Structure\Classes\Label::factory('Parameter'), $list);

		$form->add($params);
		$form->add(\Structure\Classes\Form\Input\Submit::factory('save', 'save'));
		$this->get_view()->form = $form;
	}
}