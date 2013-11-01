<?php
class Backend
{
	public static function createModule($moduleType, $regionID)
	{
		$module = new Module;

		$module->name = $moduleType;
		$module->region_id = $regionID;

		return $module->save();
	}

	public static function moveModule($moduleID, $regionID)
	{
		$module = Module::find($moduleID);

		$module->region_id = $regionID;

		return $module->save();
	}

	public static function refreshModules($modules)
	{
		$html = '<div class="drop_area"></div>';

		foreach($modules as $module)
		{
			$moduleContent = Sparky::moduleContent($module->name, 'index', array('module' => $module));
			$html .= '<div class="sparky_module_container_' . $module->id . '" data-module-id="' . $module->id . '">';
			$html .= '    <div class="sparky_module_header">';
			$html .= '        <span class="sparky_module_header_name">'.$module->name.'</span>';

			if ( ! empty($moduleContent['header']))
			{
				foreach ($moduleContent['header'] as $key => $headerLink)
				{
					if (is_array($headerLink))
					{
						$html .= '<span class="sparky_module_header_link">';
						$html .= '    <a rel="'.$headerLink['rel'].'" href="'.$headerLink['link'].'">'.$key.'</a>';
						$html .= '</span>';
					}

					if( ! is_array($headerLink))
					{
						$html .= '<span class="sparky_module_header_link">';
						$html .= '    <a href="'.$headerLink.'">'.$key.'</a>';
						$html .= '</span>';
					}
				}
			}

			if (empty($moduleContent['header']))
			{
				$html .= '<span class="sparky_module_header_link">';
				$html .= '    <a href="/m,'.$module->name.','.$module->id.'/settings">Settings</a>';
				$html .= '</span>';
			}

			$html .= '        <span class="sparky_module_delete">';
			$html .= '            <a href="#" data-module-id="'.$module->id.'"></a>';
			$html .= '        </span>';
			$html .= '        <span style="clear:both; display:block;"></span>';
			$html .= '    </div>';
			$html .= $moduleContent['html'];
			$html .= '</div>';
			$html .= '<div class="drop_area"></div>';
		}

		return $html;
	}
}