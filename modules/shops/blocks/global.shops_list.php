<?php

/**
* @Project nv Holdings
* @Author nv Holdings <contact@nv.vn>
* @Copyright (C) 2019 nv Holdings. All rights reserved
* @License: Not free read more http://nv.vn/vi/store/modules/nvtools/
* @Createdate Thu, 22 Aug 2019 14:58:08 GMT
*/

if (!defined('NV_MAINFILE')) {
	die('Stop!!!');
}

if (!nv_function_exists('nv_shops_list')) {
	/**
	* @param string $module
	* @param array $data_block
	* @param array $lang_block
	* @return string
	*/
	function nvb_config_nv_shops_list($module, $data_block, $lang_block)
	{
		global $nv_Cache, $site_mods, $nv_Request;
		$html .= '<div class="form-group">';
		$html .= '<label class="control-label col-sm-6">Số sản phẩm hiển thị:</label>';
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
		$html .= '  <label class="control-label col-sm-6">Mô tả</label>';
		$html .= '  <div class="col-sm-18"><input type="text" name="hometext" class="form-control" value="' . $data_block['hometext'] . '"/></div>';
		$html .= '</div>';

		return $html;
	}

	/**
	* @param string $module
	* @param array $lang_block
	* @return number
	*/
	function nvb_config_nv_shops_list_submit($module, $lang_block)
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

	/**
	* @param array $block_config
	* @return string
	*/
	function nv_shops_list($block_config)
	{
		global $nv_Cache, $global_config,$db_config, $site_mods, $module_info, $module_name, $module_file, $module_data, $lang_global, $catid, $home, $db,$user_info,$module_upload;

		if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $block_config['module'] . '/global.block_shops_list.tpl')) {
			$block_theme = $global_config['module_theme'];
		} elseif (file_exists(NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/' . $block_config['module'] . '/global.block_shops_list.tpl')) {
			$block_theme = $global_config['site_theme'];
		} else {
			$block_theme = 'default';
		}
		$mod_upload = $site_mods[$module_name]['module_upload'];
		$module = $block_config['module'];
		$module_upload = $site_mods[$module]['module_upload'];
		$sql = 'SELECT t1.avatar_image, t1.company_name, t1.cover_image, t2.username FROM '. $db_config['prefix'] . '_' . $block_config['module'] . '_seller_management t1 INNER JOIN ' . $db_config['prefix'] . '_users t2 ON t1.userid = t2.userid where t1.status=1 AND t1.seller_hot = 1 ORDER BY rand() limit ' . $block_config['number_item'];
		$list = $db->query($sql)->fetchAll();


		$xtpl = new XTemplate('global.block_shops_list.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $block_config['module']);
		$xtpl->assign('LANG', $lang_global);
		$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
		$xtpl->assign('BLOCK_THEME', $block_theme);
		$xtpl->assign('THEME_SITE_HREF', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA);

		foreach ($list as $key => $value_product) {
			$value_product['link'] = NV_MY_DOMAIN .'/shops/'.$value_product['username'].'/';
			
			if($value_product['avatar_image']){
					$value_product['avatar_image']  = $value_product['avatar_image'] ;
			}else{
				$value_product['avatar_image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/no_img.png';
			}
			
		
			
			$xtpl->assign( 'DATA', $value_product );
			$xtpl->parse( 'main.loop' );
		}
		
		
		

		$xtpl->parse('main');
		return $xtpl->text('main');
	}
}

if (defined('NV_SYSTEM')) {
	$content = nv_shops_list($block_config);
}
