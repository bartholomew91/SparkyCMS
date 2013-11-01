<?php
class Sparky
{
	//runs the template page for the url
	public static function run($url)
	{
		$page = Page::where('title', '=', $url)->first();
		$dom = new DOMDocument();
		@$dom->loadHTMLFile(path('templatedir').$page->template.DIRECTORY_SEPARATOR.$page->layout.EXT);
		$xpath = new DOMXPath($dom);

		$head = $xpath->query('//head');

		foreach ($head as $h)
		{
			$styleFrag = $dom->createDocumentFragment();
			$styleFrag->appendXML('
				<link type="text/css" rel="stylesheet" href="/css/sparky.css" />
				<link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/smoothness/jquery-ui.css" />
				<link type="text/css" rel="stylesheet" href="/css/elfinder/elfinder.min.css" />
				<link type="text/css" rel="stylesheet" href="/css/elfinder/theme.css" />
				');
			$jsFrag = $dom->createDocumentFragment();
			$jsFrag->appendXML('<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
								<script type="text/javascript" src="/js/sparky.js"></script>
							    <script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
							    <script type="text/javascript" src="/js/ckeditor/adapters/jquery.js"></script>
							    <script type="text/javascript" src="/js/elfinder/elfinder.min.js"></script>
							   ');

			$h->appendChild($styleFrag);
			$h->appendChild($jsFrag);
		}

		$body = $xpath->query('//body');

		foreach ($body as $b)
		{
			$adminBar = $dom->createDocumentFragment();
			$adminBar->appendXML(Response::view('admin.header'));
			$moduleBar = $dom->createDocumentFragment();
			$moduleBar->appendXML(Response::view('admin.module'));
			$b->insertBefore($moduleBar, $b->firstChild);
			$b->insertBefore($adminBar, $b->firstChild);
		}

		$regions = $xpath->query('//*[@role="region"]');

			foreach ($regions as $region)
			{
				$regionID = $region->getAttribute('data-id');
				$regionObject = Region::where_page_id($page->id)
									  ->where_name($regionID)
									  ->first();

				if ( ! is_object($regionObject) )
				{
					$regionModel = new Region;

					$regionModel->page_id = $page->id;
					$regionModel->name = $regionID;

					$regionModel->save();

					$realRegionID = $regionModel->id;
				}

				if ( is_object($regionObject) )
				{
					$realRegionID = $regionObject->id;
				}

				$region->setAttribute('data-real-id', $realRegionID);
				$dropArea = $dom->createElement('div');
				$dropArea->setAttribute('class', 'drop_area');
				$region->appendChild($dropArea);

				$modules = Module::where_region_id($realRegionID)->get();

				if (count($modules) > 0)
				{
					foreach ($modules as $module)
					{
						$moduleContent = static::moduleContent($module->name, 'index', array('module' => $module));

						$moduleBody = $dom->createElement('div');
						$moduleBody->setAttribute('class', 'sparky_module_container_' . $module->id);
						$moduleBody->setAttribute('data-module-id', $module->id);
						
						$moduleResult = $dom->createDocumentFragment();
						@$moduleResult->appendXML($moduleContent['html']);

						$moduleHeader = $dom->createElement('div');
						$moduleHeader->setAttribute('class', 'sparky_module_header');

						$moduleHeaderName = $dom->createDocumentFragment();
						$moduleHeaderName->appendXML('<span class="sparky_module_header_name">'.$module->name.'</span>');

						if ( ! empty($moduleContent['header'])) 
						{
							foreach ($moduleContent['header'] as $key => $headerLink)
							{
								if (is_array($headerLink))
								{
									$moduleHeaderName->appendXML('<span class="sparky_module_header_link">
											<a rel="'.$headerLink['rel'].'" href="'.$headerLink['link'].'">'.$key.'</a></span>
									');
								}

								if( ! is_array($headerLink))
								{
									$moduleHeaderName->appendXML('<span class="sparky_module_header_link">
										<a href="/'.$headerLink.'">'.$key.'</a></span>
									');
								}
							}
						}

						if(empty($moduleContent['header']))
						{
							$moduleHeaderName->appendXML('<span class="sparky_module_header_link"><a href="/m,' .
														 $module->name . ',' . $module->id . '/settings">Settings</a></span>');
						}

						$moduleHeaderName->appendXML('<span class="sparky_module_delete">
													  	<a href="#" data-module-id="'.$module->id.'"></a>
													  </span>
													  <span style="clear:both; display: block;"></span>'
													);


						/**
						* Create Drop Area
						**/
						$dropArea = $dom->createElement('div');
						$dropArea->setAttribute('class', 'drop_area');

						/**
						* Append Everything
						**/
						$moduleBody->appendChild($moduleHeader);
						@$moduleBody->appendChild($moduleResult);

						$moduleHeader->appendChild($moduleHeaderName);
						
						$region->appendChild($moduleBody);

						$region->appendChild($dropArea);
					}
				}

			}

		$html = $dom->saveHTML();

		return $html;
	}

	public static function module($url)
	{
		if (preg_match('/^([A-Za-z0-9\-\/]+)\/m,([A-Za-z]+),([0-9]+)\/?([A-Za-z]+)?\/?([A-Za-z0-9\/_\-]+)?\/?$/', 
				 $url, 
				 $matches))
		{
			array_shift($matches);
			$url = current($matches);
			$module = next($matches);
			$moduleID = next($matches);
			$moduleAction = next($matches);
			$moduleArgs = next($matches);

			$moduleAction = (empty($moduleAction)) ? 'index' : $moduleAction;
			$moduleArgs   = (empty($moduleArgs)) ? array() : explode('/', $moduleArgs);
			$moduleArgs['module'] = Module::where_id($moduleID)->first();

			$moduleContent = static::moduleContent($module, $moduleAction, $moduleArgs);

			if ($moduleContent['template'] === true)
			{
				$page = Page::where('title', '=', $url)->first();

				$dom = new DOMDocument();

				@$dom->loadHTMLFile(path('templatedir').DIRECTORY_SEPARATOR.str_replace('index', 'inside', $page->template).EXT);
				$xpath = new DOMXPath($dom);

				$regions = $xpath->query('//*[@role="region"]');

				foreach ($regions as $region)
				{
					$moduleResult = $dom->createDocumentFragment();
					@$moduleResult->appendXML($moduleContent['html']);
					$region->appendChild($moduleResult);
				}

				$html = $dom->saveHTML();

				return $html;
			}

			if ($moduleContent['template'] === false)
			{
				return $moduleContent['html'];
			}
		}

		if (preg_match('/^m,([A-Za-z]+),([0-9]+)\/?([A-Za-z]+)?\/?([A-Za-z0-9\/_\-]+)?\/?$/', 
				 $url, 
				 $matches))
		{
			array_shift($matches);
			$module = current($matches);
			$moduleID = next($matches);
			$moduleAction = next($matches);
			$moduleArgs = next($matches);

			$moduleAction = (empty($moduleAction)) ? 'index' : $moduleAction;
			$moduleArgs   = (empty($moduleArgs)) ? array() : explode('/', $moduleArgs);
			$moduleArgs['module'] = Module::where_id($moduleID)->first();

			$moduleContent = static::moduleContent($module, $moduleAction, $moduleArgs);

			if ($moduleContent['template'] === false)
			{
				return $moduleContent['html'];
			}
		}
	}

	public static function moduleContent($module, $action = 'index', $params = null)
	{
		return Controller::call($module.'::module@'.$action, (array) $params)->content;
	}

	public static function render($html, $header = null, $template = true)
	{
		$returnArray = array('html' => $html);

		if ( ! is_null($header))
		{
			$returnArray['header'] = $header;
		}

		$returnArray['template'] = $template;

		return $returnArray;
	}

	//creates the array for the module header URL
	public static function moduleModalURL($urls)
	{
		$urlArray = array();

		foreach ($urls as $title => $url)
		{
			$urlArray[$title] = array(
				'rel' => 'sparky_modal',
				'link' => '/m,'.$url['module']->name.','.$url['module']->id.'/'.$url['action']
			);
		}

		return $urlArray;
	}

	public static function moduleURL($title, $link)
	{
		return array(
			$title => $link
		);
	}

	public static function modalNavigation($module) {
		return Controller::call($module->name.'::module@modal', array('module' => $module))->content;
	}
}