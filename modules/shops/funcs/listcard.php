<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2022 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_MOD_SYSTEMS'))
    die('Stop!!!');

// Change status
if ($nv_Request->isset_request('change_status', 'post, get')) {
    $userid = $nv_Request->get_int('userid', 'post, get', 0);
    $content = 'NO_' . $userid;

    $query = 'SELECT ishidden FROM ' . $db_config['dbsystem'] . '.nv4_vi_multilevel_users WHERE userid=' . $userid;
    $row = $db->query($query)->fetch();
    if (isset($row['ishidden']))     {
        $ishidden = ($row['ishidden']) ? 0 : 1;
        $query = 'UPDATE ' . $db_config['dbsystem'] . '.nv4_vi_multilevel_users SET ishidden=' . intval($ishidden) . ' WHERE userid=' . $userid;
        $db->query($query);
        $content = 'OK_' . $userid;
    }
    $nv_Cache->delMod($module_name);
    include NV_ROOTDIR . '/includes/header.php';
    echo $content;
    include NV_ROOTDIR . '/includes/footer.php';
}

if ($nv_Request->isset_request('ajax_action', 'post')) {
    $userid = $nv_Request->get_int('userid', 'post', 0);
    $new_vid = $nv_Request->get_int('new_vid', 'post', 0);
    $content = 'NO_' . $userid;
    if ($new_vid > 0)     {
        $sql = 'SELECT userid FROM ' . $db_config['dbsystem'] . '.nv4_vi_multilevel_users WHERE userid!=' . $userid . ' ORDER BY weight ASC';
        $result = $db->query($sql);
        $weight = 0;
        while ($row = $result->fetch())
        {
            ++$weight;
            if ($weight == $new_vid) ++$weight;             $sql = 'UPDATE ' . $db_config['dbsystem'] . '.nv4_vi_multilevel_users SET weight=' . $weight . ' WHERE userid=' . $row['userid'];
            $db->query($sql);
        }
        $sql = 'UPDATE ' . $db_config['dbsystem'] . '.nv4_vi_multilevel_users SET weight=' . $new_vid . ' WHERE userid=' . $userid;
        $db->query($sql);
        $content = 'OK_' . $userid;
    }
    $nv_Cache->delMod($module_name);
    include NV_ROOTDIR . '/includes/header.php';
    echo $content;
    include NV_ROOTDIR . '/includes/footer.php';
}

if ($nv_Request->isset_request('delete_userid', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
    $userid = $nv_Request->get_int('delete_userid', 'get');
    $delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
    if ($userid > 0 and $delete_checkss == md5($userid . NV_CACHE_PREFIX . $client_info['session_id'])) {
        $weight=0;
        $sql = 'SELECT weight FROM ' . $db_config['dbsystem'] . '.nv4_vi_multilevel_users WHERE userid =' . $db->quote($userid);
        $result = $db->query($sql);
        list($weight) = $result->fetch(3);
        
        $db->query('DELETE FROM ' . $db_config['dbsystem'] . '.nv4_vi_multilevel_users  WHERE userid = ' . $db->quote($userid));
        if ($weight > 0)         {
            $sql = 'SELECT userid, weight FROM ' . $db_config['dbsystem'] . '.nv4_vi_multilevel_users WHERE weight >' . $weight;
            $result = $db->query($sql);
            while (list($userid, $weight) = $result->fetch(3))
            {
                $weight--;
                $db->query('UPDATE ' . $db_config['dbsystem'] . '.nv4_vi_multilevel_users SET weight=' . $weight . ' WHERE userid=' . intval($userid));
            }
        }
        $nv_Cache->delMod($module_name);
        nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete Listcard', 'ID: ' . $userid, $admin_info['userid']);
        nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
}

$row = array();
$error = array();

$q = $nv_Request->get_title('q', 'post,get');

// Fetch Limit
$show_view = false;
if (!$nv_Request->isset_request('id', 'post,get')) {
    $show_view = true;
    $per_page = 20;
    $page = $nv_Request->get_int('page', 'post,get', 1);
    $db->sqlreset()
        ->select('COUNT(*)')
        ->from('' . $db_config['dbsystem'] . '.nv4_vi_multilevel_users');

    if (!empty($q)) {
        $db->where('username LIKE :q_username OR usernameparent LIKE :q_usernameparent OR precode LIKE :q_precode OR code LIKE :q_code OR domain LIKE :q_domain OR status LIKE :q_status');
    }
    $sth = $db->prepare($db->sql());

    if (!empty($q)) {
        $sth->bindValue(':q_username', '%' . $q . '%');
        $sth->bindValue(':q_usernameparent', '%' . $q . '%');
        $sth->bindValue(':q_precode', '%' . $q . '%');
        $sth->bindValue(':q_code', '%' . $q . '%');
        $sth->bindValue(':q_domain', '%' . $q . '%');
        $sth->bindValue(':q_status', '%' . $q . '%');
    }
    $sth->execute();
    $num_items = $sth->fetchColumn();

    $db->select('*')
        ->order('weight ASC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    $sth = $db->prepare($db->sql());

    if (!empty($q)) {
        $sth->bindValue(':q_username', '%' . $q . '%');
        $sth->bindValue(':q_usernameparent', '%' . $q . '%');
        $sth->bindValue(':q_precode', '%' . $q . '%');
        $sth->bindValue(':q_code', '%' . $q . '%');
        $sth->bindValue(':q_domain', '%' . $q . '%');
        $sth->bindValue(':q_status', '%' . $q . '%');
    }
    $sth->execute();
}

$xtpl = new XTemplate('listcard.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('NV_ASSETS_DIR', NV_ASSETS_DIR);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);

$xtpl->assign('Q', $q);

if ($show_view) {
    $base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
    if (!empty($q)) {
        $base_url .= '&q=' . $q;
    }
    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    if (!empty($generate_page)) {
        $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.view.generate_page');
    }
    $number = $page > 1 ? ($per_page * ($page - 1)) + 1 : 1;
    while ($view = $sth->fetch()) {
        for($i = 1; $i <= $num_items; ++$i) {
            $xtpl->assign('WEIGHT', array(
                'key' => $i,
                'title' => $i,
                'selected' => ($i == $view['weight']) ? ' selected="selected"' : ''));
            $xtpl->parse('main.view.loop.weight_loop');
        }
        $xtpl->assign('CHECK', $view['ishidden'] == 1 ? 'checked' : '');
        $view['link_edit'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=editcart&amp;userid=' . $view['userid'];
        $view['link_delete'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_userid=' . $view['userid'] . '&amp;delete_checkss=' . md5($view['userid'] . NV_CACHE_PREFIX . $client_info['session_id']);
        $xtpl->assign('VIEW', $view);
        $xtpl->parse('main.view.loop');
    }
    $xtpl->parse('main.view');
}


if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['listcard'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
