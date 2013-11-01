<?php

//Bundle::start('text');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

//updates or creates a module
Route::post('backend/updateModule', function()
{
	//create new module in the specified region id
	if (Input::get('module_type'))
	{
		return Backend::createModule(Input::get('module_type'), Input::get('region_id'));
	}

	//move module to new region
	if (Input::get('module_id'))
	{
		return Backend::moveModule(Input::get('module_id'), Input::get('region_id'));
	}
});

//refresh all the modules html for a particular region
Route::post('backend/refreshModule', function()
{
	$modules = Module::where_region_id(Input::get('region_id'))->get();
	return Backend::refreshModules($modules);
});

Route::post('backend/deleteModule', function()
{
	$module = Module::find(Input::get('module_id'));
	return $module->delete();
});

// Route::post('backend/modal', function()
// {
// 	$module = Module::where_id(Input::get('module_id'))->first();

// 	return View::make('admin.modal')->with('module', $module);
// });

// Route::get('model/(:num)/(:text)', function($moduleID, $action) 
// {

// });

//parse direct module url (/url/m,$module_name,$module_id/action/variables)
Route::any('(^([A-Za-z0-9\-\/]+)\/m,([A-Za-z]+),([0-9]+)\/?([A-Za-z]+)?\/?([A-Za-z0-9\/_\-]+)?\/?$)', array('before' => 'checkURL', function($url) 
{
	return Sparky::module($url);
}));

//parse direct module (/m,$module_name,$module_id/action?/variables?)
Route::any('(^m,([A-Za-z]+),([0-9]+)\/?([A-Za-z]+)?\/?([A-Za-z0-9\/_\-]+)?\/?$)', function($url)
{
	return Sparky::module($url);
});

//parse the url
Route::any('(^([A-Za-z0-9\-\/]+)\/?$)', array('before' => 'checkURL', function($url) 
{
	return Sparky::run($url);
}));

/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
*/

Route::filter('before', function()
{});

Route::filter('after', function($response)
{});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('login');
});

Route::filter('checkURL', function()
{
	//get the URL slug
	$url = str_replace(Config::get('application.url').'/', '', URL::Current());

	//if the URL is for a module get just the URL and not the module information
	if (preg_match('/^([A-Za-z0-9\-\/]+)\/m,([A-Za-z]+),([0-9]+)\/?([A-Za-z]+)?\/?([A-Za-z0-9\/_\-]+)?\/?$/', $url, $matches)) {
		array_shift($matches);
		$url = current($matches);
	}

	//if the page isn't in the DB throw 404 error
	if ( (Page::where('title', '=', $url)->first()) === NULL && ! empty($url) ) {
		return Event::first('404');
	}
});