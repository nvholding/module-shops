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

if (!nv_function_exists('nvb_home_group_product')) {
	/**
	* @param string $module
	* @param array $data_block
	* @param array $lang_block
	* @return string
	*/
	function nvb_config_home_group_product($module, $data_block, $lang_block)
	{
		global $nv_Cache, $site_mods, $nv_Request,$db, $db_config;
		$sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module . '_block_cat';
		$list = $db->query($sql)->fetchAll();
		$html .= '<div class="form-group">'; 
		$html .= '<label class="control-label col-sm-6">Block sản phẩm hiển thị:</label>';
		$html .= '<div class="col-sm-9">';
		$html .= '<select name="config_blockid" class="form-control">';
		foreach ($list as $row) {
			$html .= '<option value="' . $row['bid'] . '"' . ($row['bid'] == $data_block['group_id'] ? ' selected="selected"' : '') . '>' . $row[NV_LANG_DATA .'_title'] . '</option>';
		}
		$html .= '</select>';
		$html .= '</div>';
		$html .= '</div>';
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
	// function get_info_currency($currency_id,$block_config){
	//     global $db;

	//     $list=$db->query("SELECT * FROM " . NV_PREFIXLANG . '_' . $block_config['module'] . "_unit_currency where id=".$currency_id)->fetch();

	//     return $list; 
	// }
	/**
	* @param string $module
	* @param array $lang_block
	* @return number
	*/
	function nvb_config_home_group_product_submit($module, $lang_block)
	{
		global $nv_Request;

		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['number_item'] = $nv_Request->get_int('config_number_item', 'post', 0);
		$return['config']['title_block'] = $nv_Request->get_title('title_block', 'post', '');
		$return['config']['hometext'] = $nv_Request->get_title('hometext', 'post');
		$return['config']['group_id'] = $nv_Request->get_title('config_blockid', 'post', 0);
		return $return;
	}

	/**
	* @param array $block_config
	* @return string
	*/
	function nvb_home_group_product($block_config)
	{
		global $nv_Cache, $global_config, $site_mods, $module_info, $module_name, $module_file, $module_data, $lang_global, $catid, $home, $global_array_shops_cat, $db_config,$db,$user_info;
		$module = $block_config['module'];
		if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $block_config['module'] . '/global.group_product.tpl')) {
			$block_theme = $global_config['module_theme'];
		} elseif (file_exists(NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/' . $block_config['module'] . '/global.group_product.tpl')) {
			$block_theme = $global_config['site_theme'];
		} else {
			$block_theme = 'default';
		}
		
		$sql = 'SELECT catid, parentid, lev, ' . NV_LANG_DATA . '_title AS title, ' . NV_LANG_DATA . '_alias AS alias, viewcat, numsubcat, subcatid, numlinks, ' . NV_LANG_DATA . '_description AS description, inhome, ' . NV_LANG_DATA . '_keywords AS keywords, groups_view, typeprice FROM ' . $db_config['dbsystem']. '.'. $db_config['prefix'] . '_' . $block_config['module'] . '_catalogs ORDER BY sort ASC';
            $list = $nv_Cache->db($sql, 'catid', $module);
            foreach ($list as $row) {
                $global_array_shops_cat[$row['catid']] = array(
                    'catid' => $row['catid'],
                    'parentid' => $row['parentid'],
                    'title' => $row['title'],
                    'alias' => $row['alias'],
                    'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $row['alias'],
                    'viewcat' => $row['viewcat'],
                    'numsubcat' => $row['numsubcat'],
                    'subcatid' => $row['subcatid'],
                    'numlinks' => $row['numlinks'],
                    'description' => $row['description'],
                    'inhome' => $row['inhome'],
                    'keywords' => $row['keywords'],
                    'groups_view' => $row['groups_view'],
                    'lev' => $row['lev'],
                    'typeprice' => $row['typeprice']
                );
            }
            unset($list, $row);
			$sql = "SELECT t2.*, t1.username FROM "  . $db_config['dbsystem']. '.'. $db_config['prefix'] . '_' . $block_config['module'] . "_seller_management t2 INNER JOIN " . NV_USERS_GLOBALTABLE . " t1 ON t1.userid = t2.userid";
            $list = $nv_Cache->db($sql, 'storeid', $module);
            foreach ($list as $row) {
                $global_info_shops[$row['id']] = array(
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'alias' => $row['alias'],
                    'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $row['alias'],
                    'viewcat' => $row['viewcat'],
                    'numsubcat' => $row['numsubcat'],
                    'subcatid' => $row['subcatid'],
                    'numlinks' => $row['numlinks'],
                    'description' => $row['description'],
                    'inhome' => $row['inhome'],
                    'keywords' => $row['keywords'],
                    'groups_view' => $row['groups_view'],
                    'lev' => $row['lev'],
                    'typeprice' => $row['typeprice'],
                    'username' => $row['username']
                );
            }
            unset($list, $row);
		$sql = 'SELECT t1.id, t1.listcatid,t1.homeimgfile,t1.' . NV_LANG_DATA . '_alias as alias,t1.' . NV_LANG_DATA . '_title,t1.star,t1.product_price,t1.price_special, free_ship, idsite FROM ' . $db_config['dbsystem']. '.'. $db_config['prefix'] . '_' . $block_config['module'] . '_rows t1 INNER JOIN ' .$db_config['dbsystem']. '.'. $db_config['prefix'] . '_' . $block_config['module'] . '_block t2 ON t1.id = t2.id  WHERE t1.inhome = 1 AND t2.bid = ' . $block_config['group_id'] . ' ORDER BY rand() limit ' . $block_config['number_item'];
		
		
		$list = $db->query($sql)->fetchAll();


		$xtpl = new XTemplate('global.group_product.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $block_config['module']);
		$xtpl->assign('LANG', $lang_global);
		$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
		$xtpl->assign('BLOCK_THEME', $block_theme);


		$xtpl->assign('THEME_SITE_HREF', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA);
		
		// print_r($global_config);
		foreach ($list as $key => $value_product) {
			
			$value_product['alias'] =nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_shops_cat[$value_product['listcatid']]['alias'] . '/' . $value_product['alias'] . $global_config['rewrite_exturl'], true );

			if (!empty($value_product['homeimgfile'] ) and is_file(NV_UPLOADS_REAL_DIR . '/' . $global_info_shops[$value_product['idsite']]['username'] . '/' . $block_config['module'] . '/' . $value_product['homeimgfile'] )) {
				$value_product['homeimgfile']  = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $global_info_shops[$value_product['idsite']]['username'] . '/' . $block_config['module']. '/' . $value_product['homeimgfile'] ;
			}else{
				$server = ''.$_SERVER["SERVER_NAME"];
				$value_product['homeimgfile']  ='https://'. $server .NV_BASE_SITEURL . NV_FILES_DIR . '/' . $block_config['module']. '/' . $value_product['homeimgfile'] ;
			}
			$value_product['price'] = $value_product['price'] ? $value_product['price'] : $value_product['price_special'];
			$value_product['price_format'] = number_format( $value_product['price'] ).'đ';
			$xtpl->assign( 'LOOP_PRODUCT', $value_product);
			
			if($value_product['price_special'] and $value_product['price'] < $value_product['price_special'])
			{		
				$price_special = number_format( $value_product['price_special'] ).'đ';
				$xtpl->assign( 'price_special', $price_special);
				$xtpl->parse( 'main.product.price_special' );
			}
			
			if($value_product['free_ship'])
			{		
				$xtpl->parse( 'main.product.free_ship' );
				$xtpl->assign( 'BORDER', 'picture_frames');
			}else{
				$xtpl->assign( 'BORDER', 'picture_frames1');
			}

			$xtpl->parse( 'main.product' );

		}


		$xtpl->parse('main');
		return $xtpl->text('main');
	}
	function block_get_info_user_shops_idsite($idsite)
	{
		global $db, $db_config, $module_name;
		$list = $db->query("SELECT t2.*, t1.userid as userid_shop FROM " . NV_USERS_GLOBALTABLE . " t1 INNER JOIN " . $db_config['dbsystem'] . '.' . $db_config['prefix'] . '_' . $module_name . "_seller_management t2 ON t1.userid = t2.userid where t2.id=" . $db->quote($idsite))->fetch();
		return $list;
	}
}

if (defined('NV_SYSTEM')) {
	$content = nvb_home_group_product($block_config);
}
