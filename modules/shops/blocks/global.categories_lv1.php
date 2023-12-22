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

if (!nv_function_exists('nvb_categories_lv1')) {
    /**
     * @param string $module
     * @param array $data_block
     * @param array $lang_block
     * @return string
     */
    function nvb_config_categories_lv1($module, $data_block, $lang_block)
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
    function nvb_config_categories_lv1_submit($module, $lang_block)
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
    function nvb_categories_lv1($block_config)
    {
        global $nv_Cache, $global_config, $site_mods, $module_info, $module_name, $module_file, $module_data, $lang_global, $catid, $home, $db,$db_config;

        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $block_config['module'] . '/global.categories_lv1.tpl')) {
            $block_theme = $global_config['module_theme'];
        } elseif (file_exists(NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/' . $block_config['module'] . '/global.categories_lv1.tpl')) {
            $block_theme = $global_config['site_theme'];
        } else {
            $block_theme = 'default';
        }
		
        $sql = '(SELECT ' . NV_LANG_DATA . '_title as title,' . NV_LANG_DATA . '_alias as alias,image FROM ' .$db_config['dbsystem']. '.'. $db_config['prefix'] . '_' . $block_config['module'] . '_catalogs WHERE inhome=1 AND parentid = 0 AND NOT ' . NV_LANG_DATA . '_alias like "%sac-dep-suc-khoe%" ORDER BY add_time ASC limit ' . $block_config['number_item'].')' ;
        $list = $nv_Cache->db($sql, '', $block_config['module']);


        $xtpl = new XTemplate('global.categories_lv1.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $block_config['module']);
        $xtpl->assign('LANG', $lang_global);
        $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
        $xtpl->assign('BLOCK_THEME', $block_theme);
        $xtpl->assign('THEME_SITE_HREF', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA);

        foreach ($list as $key => $value) {
         $value['image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $block_config['module'] . '/' . $value['image'];
         $value['alias'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $block_config['module'] .'/'.$value['alias'];
         $xtpl->assign('DATA', $value);
         $xtpl->parse('main.category');
     }

     $xtpl->parse('main');
     return $xtpl->text('main');
 }
}

if (defined('NV_SYSTEM')) {
    $content = nvb_categories_lv1($block_config);
}
