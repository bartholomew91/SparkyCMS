<?php

$application =  array(
	'url' => 'http://example.com',
	'asset_url' => '',
	'index' => '',
	'key' => 'INSERT YOUR KEY',
	'profiler' => false,
	'encoding' => 'UTF-8',
	'language' => 'en',
	'languages' => array(),
	'ssl' => true,
	'timezone' => 'UTC',
	'aliases' => array(
		'Auth'       	=> 'Laravel\\Auth',
		'Authenticator' => 'Laravel\\Auth\\Drivers\\Driver',
		'Asset'      	=> 'Laravel\\Asset',
		'Autoloader' 	=> 'Laravel\\Autoloader',
		'Blade'      	=> 'Laravel\\Blade',
		'Bundle'     	=> 'Laravel\\Bundle',
		'Cache'      	=> 'Laravel\\Cache',
		'Config'     	=> 'Laravel\\Config',
		'Controller' 	=> 'Laravel\\Routing\\Controller',
		'Cookie'     	=> 'Laravel\\Cookie',
		'Crypter'    	=> 'Laravel\\Crypter',
		'DB'         	=> 'Laravel\\Database',
		'Eloquent'   	=> 'Laravel\\Database\\Eloquent\\Model',
		'Event'      	=> 'Laravel\\Event',
		'File'       	=> 'Laravel\\File',
		'Filter'     	=> 'Laravel\\Routing\\Filter',
		'Form'       	=> 'Laravel\\Form',
		'Hash'       	=> 'Laravel\\Hash',
		'HTML'       	=> 'Laravel\\HTML',
		'Input'      	=> 'Laravel\\Input',
		'IoC'        	=> 'Laravel\\IoC',
		'Lang'       	=> 'Laravel\\Lang',
		'Log'        	=> 'Laravel\\Log',
		'Memcached'  	=> 'Laravel\\Memcached',
		'Paginator'  	=> 'Laravel\\Paginator',
		'Profiler'  	=> 'Laravel\\Profiling\\Profiler',
		'URL'        	=> 'Laravel\\URL',
		'Redirect'   	=> 'Laravel\\Redirect',
		'Redis'      	=> 'Laravel\\Redis',
		'Request'    	=> 'Laravel\\Request',
		'Response'   	=> 'Laravel\\Response',
		'Route'      	=> 'Laravel\\Routing\\Route',
		'Router'     	=> 'Laravel\\Routing\\Router',
		'Schema'     	=> 'Laravel\\Database\\Schema',
		'Section'    	=> 'Laravel\\Section',
		'Session'    	=> 'Laravel\\Session',
		'Str'        	=> 'Laravel\\Str',
		'Task'       	=> 'Laravel\\CLI\\Tasks\\Task',
		'URI'        	=> 'Laravel\\URI',
		'Validator'  	=> 'Laravel\\Validator',
		'View'       	=> 'Laravel\\View',
	),
);