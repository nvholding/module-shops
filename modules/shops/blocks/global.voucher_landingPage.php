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
if (!nv_function_exists('voucher_landingPage')) {
	/**
	* @param string $module
	* @param array $data_block
	* @param array $lang_block
	* @return string
	*/
	function nvb_config_voucher_landingPage($module, $data_block, $lang_block)
	{
		global $nv_Cache, $site_mods, $nv_Request;
		$html .= '<div class="form-group">';
		$html .= '<label class="control-label col-sm-6">Số voucher shop:</label>';
		$html .= '<div class="col-sm-9"><select name="voucher_shop" class="form-control">';
		for ($i = 0; $i <100 ; ++$i) {
			$html .= '<option value="' . $i . '"' . ($data_block['voucher_shop'] == $i ? ' selected="selected"' : '') . '> ' . $i . ' </option>';
		}
		$html .= "</select></div>";
		$html .= '</div>';
		
		$html .= '<div class="form-group">';
		$html .= '<label class="control-label col-sm-6">Số voucher shop HOT:</label>';
		$html .= '<div class="col-sm-9"><select name="voucher_shop_hot" class="form-control">';
		for ($i = 0; $i <100 ; ++$i) {
			$html .= '<option value="' . $i . '"' . ($data_block['voucher_shop_hot'] == $i ? ' selected="selected"' : '') . '> ' . $i . ' </option>';
		}
		$html .= "</select></div>";
		$html .= '</div>';
		
		$html .= '<div class="form-group">';
		$html .= '<label class="control-label col-sm-6">Số voucher ECNG:</label>';
		$html .= '<div class="col-sm-9"><select name="voucher_ecng" class="form-control">';
		for ($i = 0; $i <100 ; ++$i) {
			$html .= '<option value="' . $i . '"' . ($data_block['voucher_ecng'] == $i ? ' selected="selected"' : '') . '> ' . $i . ' </option>';
		}
		$html .= "</select></div>";
		$html .= '</div>';
		
		$html .= '<div class="form-group">';
		$html .= '<label class="control-label col-sm-6">Số voucher ECNG HOT:</label>';
		$html .= '<div class="col-sm-9"><select name="voucher_ecng_hot" class="form-control">';
		for ($i = 0; $i <100 ; ++$i) {
			$html .= '<option value="' . $i . '"' . ($data_block['voucher_ecng_hot'] == $i ? ' selected="selected"' : '') . '> ' . $i . ' </option>';
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
	
	function nvb_config_voucher_landingPage_submit($module, $lang_block)
	{
		global $nv_Request;

		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['voucher_shop'] = $nv_Request->get_int('voucher_shop', 'post', 0);
		$return['config']['voucher_shop_hot'] = $nv_Request->get_int('voucher_shop_hot', 'post', 0);
		$return['config']['voucher_ecng'] = $nv_Request->get_int('voucher_ecng', 'post', 0);
		$return['config']['voucher_ecng_hot'] = $nv_Request->get_int('voucher_ecng_hot', 'post', 0);
		$return['config']['title_block'] = $nv_Request->get_title('title_block', 'post', '');
		$return['config']['hometext'] = $nv_Request->get_title('hometext', 'post');
		return $return;
	}
	
	/**
	* @param array $block_config
	* @return string
	*/
	
	function voucher_landingPage($block_config)
	{
		global $nv_Cache, $global_config, $site_mods, $module_info, $module_name, $module_file, $module_data, $lang_global, $catid, $home, $db,$user_info,$db_config;

		if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $block_config['module'] . '/global.voucher_landingPage.tpl')) {
			$block_theme = $global_config['module_theme'];
		} elseif (file_exists(NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/' . $block_config['module'] . '/global.voucher_landingPage.tpl')) {
			$block_theme = $global_config['site_theme'];
		} else {
			$block_theme = 'default';
		}
		//xử lý đỗ dữ liệu
		
		$today = NV_CURRENTTIME;
		
		$list_voucher_shop = $db->query('SELECT * FROM ' .$db_config['dbsystem']. '.'. NV_PREFIXLANG . '_' . $block_config['module'] . '_voucher_shop WHERE status = 1 ORDER BY time_add DESC limit ' . $block_config['voucher_shop'])->fetchAll();
		
		$list_voucher_shop_hot = $db->query('SELECT * FROM ' .$db_config['dbsystem']. '.'. NV_PREFIXLANG . '_' . $block_config['module'] . '_voucher_shop WHERE status = 1 AND voucher_hot = 1 ORDER BY time_add DESC limit ' . $block_config['voucher_shop_hot'])->fetchAll();
		
		//$list_voucher_ecng = $db->query('SELECT * FROM ' .$db_config['dbsystem']. '.'. NV_PREFIXLANG . '_' . $block_config['module'] . '_voucher_ecng WHERE status = 1 ORDER BY time_add DESC limit ' . $block_config['voucher_ecng'])->fetchAll();
		
		//$list_voucher_ecng_hot = $db->query('SELECT * FROM ' . $db_config['dbsystem']. '.'. NV_PREFIXLANG . '_' . $block_config['module'] . '_voucher_ecng WHERE status = 1 AND voucher_hot = 1 ORDER BY time_add DESC limit ' . $block_config['voucher_ecng_hot'])->fetchAll();
		
		// print_r($list_voucher_shop);die;
		$xtpl = new XTemplate('global.voucher_landingPage.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $block_config['module']);
		$xtpl->assign('LANG', $lang_global);
		
		$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
		$xtpl->assign('BLOCK_THEME', $block_theme);
		$xtpl->assign('MODULE_NAME', $block_config['module']);
		$xtpl->assign('THEME_SITE_HREF', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA);
		
		
		//voucher shop
		if(!empty($list_voucher_shop))
		{
			foreach( $list_voucher_shop as $voucher ) {
				
				if($voucher['type_discount']){
					$voucher['discount_price'] = $voucher['discount_price'] . '%';
					
					if($voucher['maximum_discount'])
					{
						$xtpl->parse( 'main.voucher_shop.voucher_loop.maximum_discount' );
						$voucher['maximum_discount'] = number_format( $voucher['maximum_discount'] ).'đ';
						$xtpl->assign( 'maximum_discount', $voucher['maximum_discount']);
					}
					
				}
				else
				{
					$voucher['discount_price'] = number_format( $voucher['discount_price'] ).'đ'; 
				}
				$voucher['token'] = md5($voucher['id'] . $voucher['store_id'] . 'ECNG99');
				
				$voucher['avt_store'] = $db->query('SELECT avatar_image FROM ' . $db_config['dbsystem']. '.'. NV_PREFIXLANG . '_' . $block_config['module'] . '_seller_management WHERE id = ' . $voucher['store_id'])->fetchColumn();
				
				if(!$voucher['avt_store']){
					
					$voucher['avt_store'] = NV_BASE_SITEURL . NV_FILES_DIR . '/no_img.png';
				}
				
				$voucher['time_to'] = date("d-m-Y", $voucher['time_to']);
				$xtpl->assign( 'VOUCHER', $voucher);
				//check voucher khach da luu
				if($user_info){
					$check_voucher_customer = $db->query('SELECT id FROM ' . $db_config['dbsystem']. '.'. NV_PREFIXLANG . '_' . $block_config['module'] . '_voucher_wallet WHERE type_voucher = 1 AND voucherid = ' . $voucher['id'] . ' AND userid = ' . $user_info['userid'])->fetchColumn();
				}
				
				if($check_voucher_customer){
					$xtpl->parse( 'main.voucher_shop.voucher_loop.saved');
				}else{
					if(!$voucher['usage_limit_quantity']){
						$xtpl->parse( 'main.voucher_shop.voucher_loop.not_voucher');
					}else{
						$xtpl->parse( 'main.voucher_shop.voucher_loop.not_saved');
					}
					
				}
				
				$voucher['avt_store'] = $db->query('SELECT avatar_image FROM ' . $db_config['dbsystem']. '.'. NV_PREFIXLANG . '_' . $block_config['module'] . '_seller_management WHERE id = ' . $voucher['store_id'])->fetchColumn();
				
				if(!$voucher['avt_store']){
					
					$voucher['avt_store'] = NV_BASE_SITEURL . NV_FILES_DIR . '/no_img.png';
				}
				
				$xtpl->parse( 'main.voucher_shop.voucher_loop' );
			}
			$xtpl->parse( 'main.voucher_shop' );
		}
		//voucher shop HOT
		if(!empty($list_voucher_shop_hot))
		{
			foreach( $list_voucher_shop_hot as $voucher ) {
				
				if($voucher['type_discount']){
					$voucher['discount_price'] = $voucher['discount_price'] . '%';
					
					if($voucher['maximum_discount'])
					{
						$xtpl->parse( 'main.voucher_shop_hot.voucher_loop.maximum_discount' );
						$voucher['maximum_discount'] = number_format( $voucher['maximum_discount'] ).'đ'; 
					}
					
				}
				else
				{
					$voucher['discount_price'] = number_format( $voucher['discount_price'] ).'đ'; 
				}
				
				$voucher['avt_store'] = $db->query('SELECT avatar_image FROM ' . $db_config['dbsystem']. '.'. NV_PREFIXLANG . '_' . $block_config['module'] . '_seller_management WHERE id = ' . $voucher['store_id'])->fetchColumn();
				
				if(!$voucher['avt_store']){
					$voucher['avt_store'] = NV_BASE_SITEURL . NV_FILES_DIR . '/no_img.png';
				}
				
				$voucher['time_to'] = date("d-m-Y", $voucher['time_to']);
				$xtpl->assign( 'VOUCHER', $voucher);
				//check voucher khach da luu
				if($user_info){
					$check_voucher_customer = $db->query('SELECT id FROM ' . $db_config['dbsystem']. '.'. NV_PREFIXLANG . '_' . $block_config['module'] . '_voucher_wallet WHERE type_voucher = 1 AND voucherid = ' . $voucher['id'] . ' AND userid = ' . $user_info['userid'])->fetchColumn();
				}
				if($check_voucher_customer){
					$xtpl->parse( 'main.voucher_shop_hot.voucher_loop.saved');
				}else{
					if(!$voucher['usage_limit_quantity']){
						$xtpl->parse( 'main.voucher_shop_hot.voucher_loop.not_voucher');
					}else{
						$xtpl->parse( 'main.voucher_shop_hot.voucher_loop.not_saved');
					}
					
				}
				
				
				$xtpl->parse( 'main.voucher_shop_hot.voucher_loop' );
			}
			$xtpl->parse( 'main.voucher_shop_hot' );
		}
		/*
		//voucher ECNG
		if(!empty($list_voucher_ecng))
		{
			foreach( $list_voucher_ecng as $voucher ) {
				
				if($voucher['type_discount']){
					$voucher['discount_price'] = $voucher['discount_price'] . '%';
					
					if($voucher['maximum_discount'])
					{
						$xtpl->parse( 'main.voucher.maximum_discount' );
						$voucher['maximum_discount'] = number_format( $voucher['maximum_discount'] ).'đ'; 
					}
					
				}
				else
				{
					$voucher['discount_price'] = number_format( $voucher['discount_price'] ).'đ'; 
				}
				//token
				
				$voucher['time_to'] = date("d-m-Y", $voucher['time_to']);
				$xtpl->assign( 'VOUCHER', $voucher);
				//check voucher khach da luu
				$check_voucher_customer = $db->query('SELECT id FROM ' . $db_config['dbsystem']. '.'. NV_PREFIXLANG . '_' . $block_config['module'] . '_voucher_wallet WHERE voucherid = ' . $voucher['id'])->fetchColumn();
				if($check_voucher_customer){
					$xtpl->parse( 'main.voucher_ecng.voucher_loop.saved');
				}else{
					if(!$voucher['usage_limit_quantity']){
						$xtpl->parse( 'main.voucher_ecng.voucher_loop.not_voucher');
					}else{
						$xtpl->parse( 'main.voucher_ecng.voucher_loop.not_saved');
					}
					
				}
				
				
				$xtpl->parse( 'main.voucher_ecng.voucher_loop' );
			}
			$xtpl->parse( 'main.voucher_ecng' );
		}
		
		//voucher ECNG HOT
		if(!empty($list_voucher_ecng_hot))
		{
			foreach( $list_voucher_ecng_hot as $voucher ) {
				
				if($voucher['type_discount']){
					$voucher['discount_price'] = $voucher['discount_price'] . '%';
					
					if($voucher['maximum_discount'])
					{
						$xtpl->parse( 'main.voucher.maximum_discount' );
						$voucher['maximum_discount'] = number_format( $voucher['maximum_discount'] ).'đ'; 
					}
					
				}
				else
				{
					$voucher['discount_price'] = number_format( $voucher['discount_price'] ).'đ'; 
				}
				
				$voucher['time_to'] = date("d-m-Y", $voucher['time_to']);
				$xtpl->assign( 'VOUCHER', $voucher);
				//check voucher khach da luu
				$check_voucher_customer = $db->query('SELECT id FROM ' . $db_config['dbsystem']. '.'. NV_PREFIXLANG . '_' . $block_config['module'] . '_voucher_wallet WHERE voucherid = ' . $voucher['id'])->fetchColumn();
				if($check_voucher_customer){
					$xtpl->parse( 'main.voucher_ecng_hot.voucher_loop.saved');
				}else{
					if(!$voucher['usage_limit_quantity']){
						$xtpl->parse( 'main.voucher_ecng_hot.voucher_loop.not_voucher');
					}else{
						$xtpl->parse( 'main.voucher_ecng_hot.voucher_loop.not_saved');
					}
					
				}
				
				
				$xtpl->parse( 'main.voucher_ecng_hot.voucher_loop' );
			}
			$xtpl->parse( 'main.voucher_ecng_hot' );
		}*/
		
		
		$xtpl->parse('main');
		return $xtpl->text('main');
		
	}
	
}
if (defined('NV_SYSTEM')) {
	$content = voucher_landingPage($block_config);
}
