<?php

/**
 * @Project TMS Holdings
 * @Author TMS Holdings <contact@tms.vn>
 * @Copyright (C) 2020 TMS Holdings. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 22 Dec 2020 02:10:06 GMT
 */




if (!defined('NV_IS_FILE_ADMIN'))
	die('Stop!!!');
$mod = $nv_Request->get_string('mod', 'post, get', 0);

if ($mod == 'download') {
	$file_name = $nv_Request->get_string('file_name', 'get', '');

	$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;

	if (file_exists($file_path)) {
		header('Content-Description: File Transfer');
		header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename=' . $file_name);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file_path));
		readfile($file_path);
		// ob_clean();
		flush();
		nv_deletefile($file_path);

		exit();
	} else {
		die('File not exists !');
	}
}
if ($mod == 'is_download') {
	ini_set('memory_limit', '512M');
	set_time_limit(0);
	$q = $nv_Request->get_title('q', 'post,get');
	$sea_flast = $nv_Request->get_int('sea_flast', 'post,get');
	$ngay_den = $nv_Request->get_title('ngay_den', 'post,get');
	$ngay_tu = $nv_Request->get_title('ngay_tu', 'post,get');
	$status_ft = $nv_Request->get_title('status_search', 'post,get', -1);
	$catalogy = $nv_Request->get_title('catalogy', 'post,get', 0);
	//die($q .' - '. $sea_flast .' - '. $ngay_den .' - '. $ngay_tu .' - '. $status_ft .' - '. $bank_id);

	if (preg_match('/^([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})$/', $ngay_tu, $m)) {
		$_hour = $nv_Request->get_int('add_date_hour', 'post', 0);
		$_min = $nv_Request->get_int('add_date_min', 'post', 0);
		$ngay_tu = mktime($_hour, $_min, 0, $m[2], $m[1], $m[3]);
	} else {
		$ngay_tu = 0;
	}

	if (preg_match('/^([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})$/', $ngay_den, $m)) {
		$_hour = $nv_Request->get_int('add_date_hour', 'post', 23);
		$_min = $nv_Request->get_int('add_date_min', 'post', 59);
		$ngay_den = mktime($_hour, $_min, 0, $m[2], $m[1], $m[3]);
	} else {
		$ngay_den = 0;
	}

	if ($sea_flast != 9) {
		if ($ngay_tu > 0 and $ngay_den > 0) {
			$where .= ' AND time_add >= ' . $ngay_tu . ' AND time_add <= ' . $ngay_den;
		} else if ($ngay_tu > 0) {
			$where .= ' AND time_add >= ' . $ngay_tu;
		} else if ($ngay_den > 0) {
			$where .= ' AND time_add <= ' . $ngay_den;
		}
	}
	if ($status_ft > -1) {
		$where .= ' AND status =' . $status_ft;
	}
	if ($catalogy > 0) {
		$where .= ' AND catalogy =' . $catalogy;
	}
	if (!empty($q)) {
		$where .= ' AND (name LIKE "%" "' . $q . '" "%" OR phone LIKE "%" "' . $q . '" "%"  OR company_name LIKE "%" "' . $q . '" "%" OR email LIKE "%" "' . $q . '" "%" OR branch_name LIKE "%" "' . $q . '" "%" OR acount_name LIKE "%" "' . $q . '" "%" OR acount_number LIKE "%" "' . $q . '" "%")';
	}
	$db->sqlreset()
		->select('COUNT(*)')
		->from('' . $db_config['prefix'] . '_' . $module_data . '_seller_management')
		->where('1=1' . $where);
	$sth = $db->prepare($db->sql());

	$sth->execute();
	$num_items = $sth->fetchColumn();

	$db->select('*')
		->order('id DESC');
	$sth = $db->prepare($db->sql());
	$sth->execute();
	$data_array = array();
	$dataContent = array();
	$stt = 0;
	while ($view = $sth->fetch()) {
		$warehouse = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . 'warehouse_address where sell_id=' . $view['id'])->fetch();
		$user_info = get_info_user($view['userid']);
		$data_array['stt'] = ++$stt;
		$data_array['username'] = $user_info['username'];
		$data_array['email_info'] = $user_info['email'];
		$data_array['phone_info'] = $user_info['phone'];
		$data_array['company_name'] = $view['company_name'];
		$data_array['address'] = $view['address'];
		$data_array['company_code'] = $view['company_code'];
		$data_array['name'] = $view['name'];
		$data_array['phone'] = $view['phone'];
		$data_array['email'] = $view['email'];
		$data_array['name_bank'] = get_info_bank($view['bank_id'])['name_bank'];
		$data_array['branch_name'] = $view['branch_name'];
		$data_array['acount_name'] = $view['acount_name'];
		$data_array['acount_number'] = $view['acount_number'];
		$data_array['warehouse_name'] = $warehouse['name_warehouse'];
		$data_array['sender_name'] = $warehouse['name_send'];
		$data_array['sender_phone'] = $warehouse['phone_send'];
		$data_array['sender_address'] = $warehouse['address'];
		$data_array['status'] = $view['status'] ? 'Đang hoạt động' : 'Không hoạt động';
		$dataContent[] = $data_array;
	}
	$page_title = 'DANH SÁCH GIAN HÀNG';

	$Excel_Cell_Begin = 1; // Dong bat dau viet du lieu

	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(NV_ROOTDIR . '/modules/' . $module_file . '/template_excel/shops.xlsx');

	$worksheet = $spreadsheet->getActiveSheet();

	$worksheet->setTitle($page_title);

	// Set page orientation and size
	$worksheet->getPageSetup()->setOrientation(phpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
	$worksheet->getPageSetup()->setPaperSize(phpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
	$worksheet->getPageSetup()->setHorizontalCentered(true);


	$spreadsheet->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, $Excel_Cell_Begin);



	// Du lieu
	$array_key_data = array();
	$array_key_data[] = 'stt';
	$array_key_data[] = 'username';
	$array_key_data[] = 'email_info';
	$array_key_data[] = 'phone_info';
	$array_key_data[] = 'company_name';
	$array_key_data[] = 'address';
	$array_key_data[] = 'company_code';
	$array_key_data[] = 'name';
	$array_key_data[] = 'phone';
	$array_key_data[] = 'email';
	$array_key_data[] = 'name_bank';
	$array_key_data[] = 'branch_name';
	$array_key_data[] = 'acount_name';
	$array_key_data[] = 'acount_number';
	$array_key_data[] = 'warehouse_name';
	$array_key_data[] = 'sender_name';
	$array_key_data[] = 'sender_phone';
	$array_key_data[] = 'sender_address';
	$array_key_data[] = 'status';
	$pRow = $Excel_Cell_Begin;

	foreach ($dataContent as $row) {
		$pRow++;
		$columnIndex = 0;
		foreach ($array_key_data as $key_data) {
			++$columnIndex;
			$TextColumnIndex = PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
			$worksheet->setCellValue($TextColumnIndex . $pRow, $row[$key_data]);
		}
	}


	$file_name = 'danh_sach_gian_hang.xlsx';

	$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="' . $file_name . '"');
	header('Cache-Control: max-age=0');

	$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save($file_path);

	$link = NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&mod=download&file_name=' . $file_name;

	nv_jsonOutput(array('link' => $link));
}

//change seller hot
if ($nv_Request->isset_request('change_seller_hot', 'post, get')) {
	$seller_hot = $nv_Request->get_int('id_seller_hot', 'post, get', 0);

	$query = 'SELECT seller_hot, userid FROM ' . $db_config['prefix'] . '_' . $module_data . '_seller_management WHERE id=' . $seller_hot;
	$row = $db->query($query)->fetch();
	//print_r($row);die;
	if (isset($row['seller_hot'])) {

		$seller_hot = ($row['seller_hot']) ? 1 : 0;
		//print_r($seller_hot);die;
		if ($seller_hot) {
			$db->query('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_seller_management SET seller_hot = 0 WHERE userid =' . $row['userid']);
		} else {
			$db->query('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_seller_management SET seller_hot = 1 WHERE userid =' . $row['userid']);
		}
	}
}
// Change status
if ($nv_Request->isset_request('change_status', 'post, get')) {
	$id = $nv_Request->get_int('id', 'post, get', 0);
	$content = 'NO_' . $id;

	$query = 'SELECT status, userid FROM ' . $db_config['prefix'] . '_' . $module_data . '_seller_management WHERE id=' . $id;
	$row = $db->query($query)->fetch();
	if (isset($row['status'])) {

		$status = ($row['status']) ? 0 : 1;

		if ($status) {
			// active tài khoản đăng nhập
			$db->query('UPDATE ' . $db_config['dbsystem'] . '.' . $db_config['prefix'] . '_users SET active = 1 WHERE userid =' . $row['userid']);
		} else {
			// tắt active tài khoản đăng nhập
			$db->query('UPDATE ' . $db_config['dbsystem'] . '.' . $db_config['prefix'] . '_users SET active = 0 WHERE userid =' . $row['userid']);
		}

		$query = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_seller_management SET status=' . intval($status) . ', require_active = 0 WHERE id=' . $id;

		$db->query($query);
		$db->query('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_product SET inhome = ' . intval($status) . ', status=' . intval($status) . ' WHERE inhome > -1 AND store_id=' . $id);

		$content = 'OK_' . $id;
	}
	$nv_Cache->delMod($module_name);
	include NV_ROOTDIR . '/includes/header.php';
	echo $content;
	include NV_ROOTDIR . '/includes/footer.php';
}

if ($nv_Request->isset_request('ajax_action', 'post')) {
	$id = $nv_Request->get_int('id', 'post', 0);
	$new_vid = $nv_Request->get_int('new_vid', 'post', 0);
	$content = 'NO_' . $id;
	if ($new_vid > 0) {
		$sql = 'SELECT id FROM ' . $db_config['prefix'] . '_' . $module_data . '_seller_management WHERE id!=' . $id . ' ORDER BY weight ASC';
		$result = $db->query($sql);
		$weight = 0;
		while ($row = $result->fetch()) {
			++$weight;
			if ($weight == $new_vid) ++$weight;
			$sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_seller_management SET weight=' . $weight . ' WHERE id=' . $row['id'];
			$db->query($sql);
		}
		$sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_seller_management SET weight=' . $new_vid . ' WHERE id=' . $id;
		$db->query($sql);
		$content = 'OK_' . $id;
	}
	$nv_Cache->delMod($module_name);
	include NV_ROOTDIR . '/includes/header.php';
	echo $content;
	include NV_ROOTDIR . '/includes/footer.php';
}

if ($nv_Request->isset_request('delete_id', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
	$id = $nv_Request->get_int('delete_id', 'get');
	$delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
	if ($id > 0 and $delete_checkss == md5($id . NV_CACHE_PREFIX . $client_info['session_id'])) {
		$weight = 0;
		$sql = 'SELECT weight FROM ' . $db_config['prefix'] . '_' . $module_data . '_seller_management WHERE id =' . $db->quote($id);
		$result = $db->query($sql);
		list($weight) = $result->fetch(3);

		$db->query('DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_seller_management  WHERE id = ' . $db->quote($id));
		if ($weight > 0) {
			$sql = 'SELECT id, weight FROM ' . $db_config['prefix'] . '_' . $module_data . '_seller_management WHERE weight >' . $weight;
			$result = $db->query($sql);
			while (list($id, $weight) = $result->fetch(3)) {
				$weight--;
				$db->query('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_seller_management SET weight=' . $weight . ' WHERE id=' . intval($id));
			}
		}
		$nv_Cache->delMod($module_name);
		nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete Seller_management', 'ID: ' . $id, $admin_info['userid']);
		nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
	}
}

$where = '';
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
$q = $nv_Request->get_title('q', 'post,get');
$sea_flast = $nv_Request->get_int('sea_flast', 'post,get');
$ngay_den = $nv_Request->get_title('ngay_den', 'post,get');
$ngay_tu = $nv_Request->get_title('ngay_tu', 'post,get');
$status_ft = $nv_Request->get_title('status_search', 'post,get', -1);
$catalogy = $nv_Request->get_title('catalogy', 'post,get', 0);

if (preg_match('/^([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})$/', $ngay_tu, $m)) {
	$_hour = $nv_Request->get_int('add_date_hour', 'post', 0);
	$_min = $nv_Request->get_int('add_date_min', 'post', 0);
	$ngay_tu = mktime($_hour, $_min, 0, $m[2], $m[1], $m[3]);
} else {
	$ngay_tu = 0;
}

if (preg_match('/^([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})$/', $ngay_den, $m)) {
	$_hour = $nv_Request->get_int('add_date_hour', 'post', 23);
	$_min = $nv_Request->get_int('add_date_min', 'post', 59);
	$ngay_den = mktime($_hour, $_min, 0, $m[2], $m[1], $m[3]);
} else {
	$ngay_den = 0;
}

if ($sea_flast != 9) {
	if ($ngay_tu > 0 and $ngay_den > 0) {

		$where .= ' AND s.time_add >= ' . $ngay_tu . ' AND s.time_add <= ' . $ngay_den;
		$base_url .= '&ngay_tu=' . date('d-m-Y', $ngay_tu) . '&ngay_den=' . date('d-m-Y', $ngay_den);
	} else if ($ngay_tu > 0) {
		$where .= ' AND s.time_add >= ' . $ngay_tu;
		$base_url .= '&ngay_tu=' . date('d-m-Y', $ngay_tu) . '&ngay_den=' . date('d-m-Y', $ngay_den);
	} else if ($ngay_den > 0) {
		$where .= ' AND s.time_add <= ' . $ngay_den;
		$base_url .= '&ngay_tu=' . date('d-m-Y', $ngay_tu) . '&ngay_den=' . date('d-m-Y', $ngay_den);
	}
}
if ($status_ft > -1) {
	$where .= ' AND s.status =' . $status_ft;
	$base_url .= '&status_search=' . $status_ft;
}
if ($catalogy > 0) {
	$where .= ' AND s.catalogy =' . $catalogy;
	$base_url .= '&catalogy=' . $catalogy;
}
if (!empty($q)) {
	$where .= ' AND (s.name LIKE "%" "' . $q . '" "%" OR s.phone LIKE "%" "' . $q . '" "%" OR s.company_name LIKE "%" "' . $q . '" "%" OR email LIKE "%" "' . $q . '" "%" OR branch_name LIKE "%" "' . $q . '" "%" OR acount_name LIKE "%" "' . $q . '" "%" OR acount_number LIKE "%" "' . $q . '" "%")';
	$base_url .= '&q=' . $q;
}
// Fetch Limit
$show_view = false;
if (!$nv_Request->isset_request('id', 'post,get')) {
	$show_view = true;
	$per_page = 20;
	$page = $nv_Request->get_int('page', 'post,get', 1);
	$db->sqlreset()
		->select('COUNT(*)')
		->from('' . $db_config['prefix'] . '_' . $module_data . '_seller_management as s')
		->where('1=1 ' . $where);

	$sth = $db->prepare($db->sql());

	$sth->execute();
	$num_items = $sth->fetchColumn();

	$db->select('s.*')
		->join('LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . '_seller_management as u ON s.userid=u.userid')
		->order('id DESC')
		->limit($per_page)
		->offset(($page - 1) * $per_page);
	$sth = $db->prepare($db->sql());

	$sth->execute();
}

$xtpl = new XTemplate('seller_management.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('NV_ASSETS_DIR', NV_ASSETS_DIR);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);

$xtpl->assign('validate_phone', "^\d{4}-\d{3}-\d{3}$");
$xtpl->assign('seller_management_add', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=seller_management_add');


$xtpl->assign('Q', $q);

if ($show_view) {
	$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
	if (!empty($generate_page)) {
		$xtpl->assign('NV_GENERATE_PAGE', $generate_page);
		$xtpl->parse('main.view.generate_page');
	}
	$number = $page > 1 ? ($per_page * ($page - 1)) + 1 : 1;
	//$real_week = nv_get_week_from_time(NV_CURRENTTIME);
	$week = $real_week[0];
	$year = $real_week[1];
	$this_year = $real_week[1];
	$time_per_week = 86400 * 7;
	$time_start_year = mktime(0, 0, 0, 1, 1, $year);
	$time_first_week = $time_start_year - (86400 * (date('N', $time_start_year) - 1));

	$tuannay = array(
		'from' => nv_date('d-m-Y', $time_first_week + ($week - 1) * $time_per_week),
		'to' => nv_date('d-m-Y', $time_first_week + ($week - 1) * $time_per_week + $time_per_week - 1),
	);
	$tuantruoc = array(
		'from' => nv_date('d-m-Y', $time_first_week + ($week - 2) * $time_per_week),
		'to' => nv_date('d-m-Y', $time_first_week + ($week - 2) * $time_per_week + $time_per_week - 2),
	);
	$tuankia = array(
		'from' => nv_date('d-m-Y', $time_first_week + ($week - 3) * $time_per_week),
		'to' => nv_date('d-m-Y', $time_first_week + ($week - 3) * $time_per_week + $time_per_week - 3),
	);

	$thangnay = array(
		'from' => date('d-m-Y', strtotime('first day of this month')),
		'to' => date('d-m-Y', strtotime('last day of this month')),
	);
	$thangtruoc = array(
		'from' => date('d-m-Y', strtotime('first day of last month')),
		'to' => date('d-m-Y', strtotime('last day of last month')),
	);
	$namnay = array(
		'from' => date('d-m-Y', strtotime('first day of january this year')),
		'to' => date('d-m-Y', strtotime('last day of december this year')),
	);
	$namtruoc = array(
		'from' => date('d-m-Y', strtotime('first day of january last year')),
		'to' => date('d-m-Y', strtotime('last day of december last year')),
	);
	$xtpl->assign('TUANNAY', $tuannay);

	$xtpl->assign('TUANTRUOC', $tuantruoc);

	$xtpl->assign('TUANKIA', $tuankia);

	$xtpl->assign('HOMNAY', date('d-m-Y', NV_CURRENTTIME));
	$xtpl->assign('HOMQUA', date('d-m-Y', strtotime('yesterday')));
	$xtpl->assign('THANGNAY', $thangnay);

	$xtpl->assign('THANGTRUOC', $thangtruoc);

	$xtpl->assign('NAMNAY', $namnay);

	$xtpl->assign('NAMTRUOC', $namtruoc);

	// danh sách ngành hàng 
	foreach ($catalogy_main_lev0 as $catalogy_new) {
		$xtpl->assign('OPTION', array(
			'key' => $catalogy_new['id'],
			'title' => $catalogy_new['name'],
			'selected' => ($catalogy_new['id'] == $catalogy) ? ' selected="selected"' : ''
		));
		$xtpl->parse('main.view.catalogy');
	}

	if ($sea_flast == '1') {
		$xtpl->assign('SELECT1', 'selected="selected"');
	}
	if ($sea_flast == '2') {
		$xtpl->assign('SELECT2', 'selected="selected"');
	}
	if ($sea_flast == '3') {
		$xtpl->assign('SELECT3', 'selected="selected"');
	}
	if ($sea_flast == '4') {
		$xtpl->assign('SELECT4', 'selected="selected"');
	}
	if ($sea_flast == '5') {
		$xtpl->assign('SELECT5', 'selected="selected"');
	}
	if ($sea_flast == '6') {
		$xtpl->assign('SELECT6', 'selected="selected"');
	}
	if ($sea_flast == '7') {
		$xtpl->assign('SELECT7', 'selected="selected"');
	}
	if ($sea_flast == '8') {
		$xtpl->assign('SELECT8', 'selected="selected"');
	}
	if ($sea_flast == '9') {
		$xtpl->assign('SELECT9', 'selected="selected"');
	}
	$status_filt = array();
	$status_filt[] = array('id' => -1, 'text' => 'Tất cả trạng thái');
	$status_filt[] = array('id' => 0, 'text' => 'Ngưng Hoạt động');
	$status_filt[] = array('id' => 1, 'text' => 'Hoạt động');

	foreach ($status_filt as $filt_stt) {
		if ($filt_stt['id'] == $status_ft) {
			$filt_stt['selected'] = 'selected';
		}
		$xtpl->assign('status_filt', $filt_stt);
		$xtpl->parse('main.view.status_filt');
	}
	while ($view = $sth->fetch()) {
		for ($i = 1; $i <= $num_items; ++$i) {
			$xtpl->assign('WEIGHT', array(
				'key' => $i,
				'title' => $i,
				'selected' => ($i == $view['weight']) ? ' selected="selected"' : ''
			));
			$xtpl->parse('main.view.loop.weight_loop');
		}
		//print_r($view);die;
		if (!empty($view['image_before']) and is_file(NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/business_license/' . $view['image_before'])) {
			$view['image_before']  = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/business_license/' . $view['image_before'];
		}
		if (!empty($view['image_after']) and is_file(NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/business_license/' . $view['image_after'])) {
			$view['image_after']  = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/business_license/' . $view['image_after'];
		}
		$view['address'] = $view['address'];
		//$view['username'] = get_info_user($view['userid'])['username'];
		//$view['user_add'] = get_info_user($view['user_add'])['username'];
		$view['time_add'] = date('d/m/Y H:i', $view['time_add']);
		if (empty($view['user_edit'])) {
			$view['user_edit'] = 'Chưa cập nhật';
			$view['time_edit'] = 'Chưa cập nhật';
		} else {
			//$view['user_edit'] = get_info_user($view['user_edit'])['username'];
			$view['time_edit'] = date('d/m/Y H:i', $view['time_edit']);
		}
		$xtpl->assign('CHECK', $view['status'] == 1 ? 'checked' : '');

		$xtpl->assign('CHECK_SELLER_HOT', $view['seller_hot'] == 1 ? 'checked' : '');
		//$view['bank_code'] = get_info_bank($view['bank_id'])['bank_code'];
		//$view['bank_id'] = get_info_bank($view['bank_id'])['name_bank'];
		$view['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=seller_management_add&amp;id=' . $view['id'];
		$view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_id=' . $view['id'] . '&amp;delete_checkss=' . md5($view['id'] . NV_CACHE_PREFIX . $client_info['session_id']);
		$view['time_require'] = date('d/m/Y - H:i:s', $view['time_require']);

		if ($view['catalogy'])
			$view['catalogy'] = $catalogy_main_lev0[$view['catalogy']]['name'];
		else
			$view['catalogy'] = 'Tất cả';

		$view['total_order'] = $db->query('SELECT sum(order_total) as total FROm ' . $db_config['prefix'] . '_' . $module_data . '_orders where shop_id=' . $view['id'])->fetchColumn();
		$view['total_order_plus'] = $db->query('SELECT sum(order_total) as total FROm ' . $db_config['prefix'] . '_' . $module_data . '_orders where shop_id=' . $view['id'] . ' and plus_money=1')->fetchColumn();
		if (empty($view['total_order'])) {
			$view['total_order'] = 0;
		}
		if (empty($view['total_order_plus'])) {
			$view['total_order_plus'] = 0;
		}
		$view['total_order_discount'] = $view['total_order'] * 95 / 100;
		$view['total_order_plus'] = $view['total_order_plus'] * 95 / 100;
		$view['remaining_order'] = $view['total_order_discount'] - $view['total_order_plus'];
		$view['total_order'] = number_format($view['total_order']);
		$view['total_order_discount'] = number_format($view['total_order_discount']);
		$view['total_order_plus'] = number_format($view['total_order_plus']);
		$view['remaining_order'] = number_format($view['remaining_order']);
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

$page_title = $lang_module['seller_management'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
