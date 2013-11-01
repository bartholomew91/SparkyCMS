<?php
class Text_Module_Controller extends Base_Controller
{
	//use rest methods
	public $restful = true;

	//the get index function for the module
	public function get_index($module)
	{
		$text = Text\Models\Module::where_module_id($module->id)->first();

		return Sparky::render(
			View::make('text::index')->with('text', $text), //the modules view
			Sparky::moduleModalURL(                         //----------------------
				array('Edit' => array(                      // The header URLs
					'module' => $module, 'action' => 'edit' // for the module
				)),                                         // for this particular
				array('Test' => array(                      // view.
					'module' => $module, 'action' => 'test' //
				))                                          //
			)                                               //----------------------
		);
	}

	public function get_for($module)
	{
		$html = "";

		for($i = 0; $i < 50; ++$i)
		{
			$html .= $i . '<br/>';
		}

		return Sparky::render($html);
	}

	//the post index function for the module
	public function post_index($module)
	{
		$text = Text\Models\Module::where_module_id($module->id)->first();

		return Sparky::render(
			View::make('text::index')->with('text', $text),
			Sparky::moduleModalURL(
				array('Edit' => array(
					'module' => $module, 'action' => 'edit'
				)),
				array('Test' => array(
					'module' => $module, 'action' => 'test'
				))
			)
		);
	}

	//the get edit function for the module
	public function get_edit($module)
	{
		$text = Text\Models\Module::where_module_id($module->id)->first();

		return Sparky::render(
			View::make('text::edit')
			    ->with('text', $text)
			    ->with('module', $module),
			   null,
			   false
		);
	}

	public function get_test($module)
	{
		return Sparky::render(
			"<h4>Test</h4>",
			null,
			false
		);
	}

	//the post save function for the module
	public function post_save()
	{
		$text = Text\Models\Module::where_module_id(Input::get('module_id'))->first();

		if (is_null($text))
		{
			$text = new Text\Models\Module;

			$text->module_id = Input::get('module_id');
			$text->content = Cleaninput::basic(Input::get('content'));
		}


		if ( ! is_null($text))
		{
			$text->content = Cleaninput::basic(Input::get('content'));
		}

		return $text->save();
	}

	//setup the modal for this module
	public function get_modal($module)
	{
		//the modal side navigation for the module
		return array(
			'Edit' => 'm,'.$module->name.','.$module->id.'/edit',
			'Test' => 'm,'.$module->name.','.$module->id.'/test',
			'Blah' => 'm,'.$module->name.','.$module->id.'/blah',
			'Meh'  => 'm,'.$module->name.','.$module->id.'/meh',
		);
	}
}