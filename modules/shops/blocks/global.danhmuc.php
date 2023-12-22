<?php

/**
	* @Project TMS Holdings
	* @Author TMS Holdings <contact@tms.vn>
	* @Copyright (C) 2019 TMS Holdings. All rights reserved
	* @License: Not free read more http://tms.vn/vi/store/modules/nvtools/
	* @Createdate Thu, 22 Aug 2019 14:58:08 GMT
*/

if (!defined('NV_MAINFILE')) {
	die('Stop!!!');
}

if (!nv_function_exists('nvb_home_danhmuc')) {
	/**
		* @param string $module
		* @param array $data_block
		* @param array $lang_block
		* @return string
	*/
	function nvb_config_home_danhmuc($module, $data_block, $lang_block)
	{
		global $nv_Cache, $site_mods, $nv_Request;
		$html .= '<div class="form-group">';
		$html .= '<label class="control-label col-sm-6">Số chuyên mục hiển thị:</label>';
		$html .= '<div class="col-sm-9"><select name="config_number_item" class="form-control">';
		for ($i = 0; $i < 20; ++$i) {
			$html .= '<option value="' . $i . '"' . ($data_block['number_item'] == $i ? ' selected="selected"' : '') . '> ' . $i . ' </option>';
		}
		$html .= "</select></div>";
		$html .= '</div>';
		$html .='<div class="form-group">';
		$html .= '<label class="control-label col-sm-6">Tiêu đề block:</label>';
		$html .= '<div class="col-sm-9"><input type="text" class="form-control" name="title_block" value="' . $data_block['title_block'] . '"/></div>';
		$html .= '</div>';
		$html .= '<div class="form-group">';
		$html .= '	<label class="control-label col-sm-6">Mô tả</label>';
		$html .= '	<div class="col-sm-18"><input type="text" name="hometext" class="form-control" value="' . $data_block['hometext'] . '"/></div>';
		$html .= '</div>';
		
		return $html;
	}
	
	/**
		* @param string $module
		* @param array $lang_block
		* @return number
	*/
	function nvb_config_home_danhmuc_submit($module, $lang_block)
	{
		global $nv_Request;
		
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['number_item'] = $nv_Request->get_int('config_number_item', 'post', 0);
		$return['config']['title_block'] = $nv_Request->get_title('title_block', 'post', '');
		$return['config']['hometext'] = $nv_Request->get_title('hometext', 'post');
		return $return;
	}
	
	function get_html_catid_all_block($array, $level)
	{
		global $html_catid;
		
		$level++;
		
		$html_catid .= '<ul id="ul_cap'. $level .'"  >';
		foreach($array as $item)
		{	
			//print_r($array);
			$html_catid .= '<li id="li_cap'. $level .'" >';
			$html_catid .= '<a  href="' . $item['link'] . '" class="p-1">';
			$html_catid .= '<div>';
			if($item['parrent_id'] == 0)
			{
				$html_catid .= '<image src='. $item['image'] .' class="mr-1">';
			}
			
			$html_catid .= $item['name'];
			$html_catid .= '</div>';
			$html_catid .= '</a>';
			
			
			if($item['sub'] and $level <3)
			{	
				$html_catid .= '<a id="icon_right" href="#" class="p-1">';
				$html_catid .= '<i  class="fa fa-angle-right pl-3 mr-2 " aria-hidden="true "></i>';
				$html_catid .= '</a>';
			}
			if($item['sub'])
			{
				get_html_catid_all_block($item['sub'], $level);
			}
			$html_catid .= '</li>';
			
		}
		$html_catid .= '</ul>';
		
		
		
		return $html_catid;
	}
	
	function category_other_img_block($other_image)
	{
		
		if(empty($other_image))
		return array();
		$other_image_ar = explode('|', $other_image);
		
		foreach ($other_image_ar as $key => $value) 
		{	
			$ar = explode(';;',$value);
			
			if($ar[1])
			$other_image_ar[$key] = $ar[1];
			
		}
		return $other_image_ar;
	}
	
	function get_categories_all_lev_block($id, $block_config)
	{
		global $db, $module_upload, $module, $module_name, $db_config;
		
		$arr_cata = array();
		
		$categorys = $db->query('SELECT id, name, alias, image, other_image, brand, parrent_id FROM ' . $db_config['dbsystem'] . '.' . NV_PREFIXLANG . '_' . $block_config['module'] .'_categories WHERE status = 1 AND inhome = 1 AND parrent_id =' . $id .' ORDER BY weight ASC')->fetchAll();
		
		
		foreach($categorys as $category)
		{
			
			// tạo link
			$category['link'] = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $block_config['module'] . '&amp;' . NV_OP_VARIABLE . '=' . $category['alias'], true );
			
			// lấy danh sách con
			$category['image'] = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $block_config['module'] . '/' . $category['image'];
			$category['other_image'] = category_other_img_block($category['other_image']);
			
			$category['brand'] = brand_category_block($category['brand'], $block_config);
			
			$category['sub'] = get_categories_all_lev_block($category['id'], $block_config);
			$arr_cata[$category['id']] = $category;
			
		}
		
		return $arr_cata;
		
	}
	
	function brand_category_block($brand_string , $block_config)
	{
		global $db, $module_upload, $db_config;
		
		$arr = array();
		
		if(empty($brand_string))
		{
			return $arr;
		}
		
		$arr_br = explode('|', $brand_string);
		
		
		if(!empty($arr_br) and !empty($brand_string))
		{
			$list_brand = $db->query('SELECT id, title, logo FROM ' . $db_config['dbsystem'] . '.' . NV_PREFIXLANG . '_' . $block_config['module'] . '_brand WHERE id IN('. implode(',',$arr_br).') AND status =1 ORDER BY weight ASC')->fetchAll();
			
			foreach($list_brand as $brand)
			{
				if(!empty($brand['logo']))
				{
					$brand['logo'] = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $brand['logo'];
					
					$arr[] = $brand;
				}
			}
			
		}
		
		return $arr;
	}
	/**
		* @param array $block_config
		* @return string
	*/
	function nvb_home_danhmuc($block_config)
	{
		
		global $nv_Cache, $global_config, $site_mods, $module_info, $module_name, $module_file, $module_data, $lang_global, $catid, $home, $db,$db_config, $redis;
		
		if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $block_config['module'] . '/global.danhmuc.tpl')) {
			$block_theme = $global_config['module_theme'];
			} elseif (file_exists(NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/' . $block_config['module'] . '/global.danhmuc.tpl')) {
			$block_theme = $global_config['site_theme'];
			} else {
			$block_theme = 'default';
		}
		
		
		
		$xtpl = new XTemplate('global.danhmuc.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $block_config['module']);
		$xtpl->assign('LANG', $lang_global);
		$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
		$xtpl->assign('BLOCK_THEME', $block_theme);
		$xtpl->assign('THEME_SITE_HREF', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA);
		
		// xử lý lấy tất cả danh mục sản phẩm
		if(!$redis->exists('catalogy_main_all_lev'))
		{
			$catalogys = get_categories_all_lev_block(0, $block_config);
			$redis->set('catalogy_main_all_lev', json_encode($catalogys));	
		}
		$global_categories = json_decode($redis->get('catalogy_main_all_lev'),true);
		//print_r($global_categories);
		$html = get_html_catid_all_block($global_categories, 0);
		$xtpl->assign('html', $html);
		
		$xtpl->parse('main');
		return $xtpl->text('main');
	}
}

if (defined('NV_SYSTEM')) {
	$content = nvb_home_danhmuc($block_config);
}
