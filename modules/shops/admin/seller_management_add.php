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
$row = array();
$error = array();
$row['id'] = $nv_Request->get_int('id', 'post,get', 0);
if ($nv_Request->isset_request('submit', 'post')) {
	$row['company_name'] = $nv_Request->get_title('company_name', 'post', '');
	$row['company_code'] = $nv_Request->get_title('company_code', 'post', '');
	$row['store_code'] = $nv_Request->get_title('store_code', 'post', '');
	$row['address_shop'] = $nv_Request->get_title('address_shop', 'post', '');
	$row['province_id_shop'] = $nv_Request->get_int('province_id_shop', 'post', 0);
	$row['district_id_shop'] = $nv_Request->get_int('district_id_shop', 'post', 0);
	$row['ward_id_shop'] = $nv_Request->get_int('ward_id_shop', 'post', 0);
	$row['name'] = $nv_Request->get_title('name', 'post', '');
	$row['phone'] = $nv_Request->get_title('phone', 'post', '');
	$row['email'] = $nv_Request->get_title('email', 'post', '');
	$row['email'] = strtolower($row['email']);

	$row['username'] = $nv_Request->get_title('username', 'post', '');
	$row['username'] = strtolower($row['username']);

	$row['password1'] = $nv_Request->get_title('password', 'post', '');
	$row['password2'] = $nv_Request->get_title('password2', 'post', '');
	$row['email_representative'] = $nv_Request->get_title('email_representative', 'post', '');
	$row['phone_representative'] = $nv_Request->get_title('phone_representative', 'post', '');
	$row['image_before'] = $nv_Request->get_title('image_before', 'post', '');
	$row['description_shop'] = $nv_Request->get_title('description_shop', 'post', '');


	$row['image_banner'] = '';

	$image_banner = $nv_Request->get_typed_array('img', 'post', 'string');
	$ii = 1;
	foreach ($image_banner as $key => $value) {
		if ($ii == count($image_banner)) {
			$row['image_banner'] .= $value;
		} else {
			$row['image_banner'] .= $value . ',';
		}
		$ii++;
	}

	$row['avatar_image'] = $nv_Request->get_title('avatar_image', 'post', '');
	$row['cover_image'] = $nv_Request->get_title('cover_image', 'post', '');
	$row['image_after'] = $nv_Request->get_title('image_after', 'post', '');
	$row['bank_id'] = $nv_Request->get_int('bank_id', 'post', 0);
	$row['catalogy'] = $nv_Request->get_int('catalogy', 'post', 0);
	$row['acount_name'] = $nv_Request->get_title('acount_name', 'post', '');
	$row['acount_number'] = $nv_Request->get_title('acount_number', 'post', '');
	$row['branch_name'] = $nv_Request->get_title('branch_name', 'post', '');
	//

	$row['warehouse_id'] = $nv_Request->get_int('warehouse_id', 'post', '');
	$row['name_warehouse'] = $nv_Request->get_title('name_warehouse', 'post', '');
	$row['name_send'] = $nv_Request->get_title('name_send', 'post', '');
	$row['phone_send'] = $nv_Request->get_title('phone_send', 'post', '');
	$row['province_id'] = $nv_Request->get_title('province_id', 'post', '');
	$row['district_id'] = $nv_Request->get_title('district_id', 'post', '');
	$row['ward_id'] = $nv_Request->get_title('ward_id', 'post', '');
	$row['address'] = $nv_Request->get_title('address', 'post', '');
	$row['centerlat'] = $nv_Request->get_title('centerlat', 'post', 0);
	$row['centerlng'] = $nv_Request->get_title('centerlng', 'post', 0);
	$row['lat'] = $nv_Request->get_title('lat', 'post', '');
	$row['lng'] = $nv_Request->get_title('lng', 'post', '');
	$row['maps_mapzoom'] = $nv_Request->get_title('maps_mapzoom', 'post', 0);
	$row['userid'] = $nv_Request->get_int('userid', 'post', 0);
	
	// kiểm tra store_code không được trùng
	$where = '';
	if ($row['id'] > 0) {
		$where = ' AND id !=' . $row['id'];
	}

	$exits_company_code = $db->query('SELECT count(*) FROM ' . $db_config['prefix'] . '_' . $module_data . '_seller_management where store_code=' . $db->quote($row['store_code']) . $where)->fetchColumn();
	if ($exits_company_code) {
		$error[] = 'Mã gian hàng không được trùng';
	}


	// kiểm tra company_code không được trùng
	$where = '';
	if ($row['id'] > 0) {
		$where = ' AND id !=' . $row['id'];
	}

	$exits_company_code = $db->query('SELECT count(*) FROM ' . $db_config['prefix'] . '_' . $module_data . '_seller_management where company_code=' . $db->quote($row['company_code']) . $where)->fetchColumn();
	if ($exits_company_code) {
		$error[] = 'Mã doanh nghiệp không được trùng';
	}



	 if (empty($row['company_name'])) {
		$error[] = $lang_module['error_required_company_name'];
	} elseif (empty($row['company_code'])) {
		$error[] = $lang_module['error_required_company_code'];
	} elseif (empty($row['store_code'])) {
		$error[] = $lang_module['error_required_store_code'];
	} elseif (empty($row['address'])) {
		$error[] = $lang_module['error_required_address'];
	}/*  elseif (empty($row['province_id'])) {
		$error[] = $lang_module['error_required_province_id'];
	} elseif (empty($row['district_id'])) {
		$error[] = $lang_module['error_required_district_id'];
	} elseif (empty($row['ward_id'])) {
		$error[] = $lang_module['error_required_ward_id'];
	} */ elseif (empty($row['name'])) {
		$error[] = $lang_module['error_required_name'];
	}/*  elseif (empty($row['phone']) && ) {
		$error[] = $lang_module['error_required_phone'];
	} elseif (empty($row['email'])) {
		$error[] = $lang_module['error_required_email'];
	} */ elseif (empty($row['image_before'])) {
		$error[] = $lang_module['error_required_image_before'];
	} elseif (empty($row['bank_id'])) {
		$error[] = $lang_module['error_required_bank_id'];
	} elseif (empty($row['acount_name'])) {
		$error[] = $lang_module['error_required_acount_name'];
	} elseif (empty($row['acount_number'])) {
		$error[] = $lang_module['error_required_acount_number'];
	} elseif (empty($row['branch_name'])) {
		$error[] = $lang_module['error_required_branch_id'];
	}/*  elseif (empty($row['username'])) {
		$error[] = 'Vui lòng nhập tên đăng nhập';
	} */ elseif (empty($row['email_representative'])) {
		$error[] = 'Vui lòng nhập email người đại diện';
	} elseif (empty($row['phone_representative'])) {
		$error[] = 'Vui lòng nhập số điện thoại người đại diện';
	}
	 
	// địa chỉ ngắn gọn
	$short_address = $row['address'];
	//$address = get_full_address($row['ward_id'], $row['district_id'], $row['province_id']);
	$row['address'] = $row['address'] . $address;
	
	if (empty($error)) {

		if ($row['id'] > 0) {
			$check_phone = $db->query('SELECT count(*) FROM ' . NV_USERS_GLOBALTABLE . ' where phone=' . $db->quote($row['phone']) . ' and userid!=' . $row['userid'])->fetchColumn();
			if ($check_phone > 0) {
				$error[] = 'Số điện thoại đã tồn tại';
			}
		} else {
			$check_phone = $db->query('SELECT count(*) FROM ' . NV_USERS_GLOBALTABLE . ' where phone=' . $db->quote($row['phone']))->fetchColumn();
			if ($check_phone > 0) {
				$error[] = 'Số điện thoại đã tồn tại';
			}
		}
	}

	if ($row['id'] == 0) {
		
	} else {
		
	}
	
	if (empty($error)) {
		try {
			if (empty($row['id'])) {
			
				$row['user_add'] = $admin_info['userid'];
				$row['time_add'] = NV_CURRENTTIME;

				$stmt = $db->prepare('INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_seller_management (description_shop,image_banner,avatar_image,cover_image,userid, company_name, store_code, company_code, address, province_id, district_id, ward_id, name, phone, email, image_before, image_after, bank_id, catalogy, acount_name, acount_number, branch_name,user_add, time_add, user_edit, time_edit, status, weight) VALUES (:description_shop,:image_banner,:avatar_image, :cover_image,:userid, :company_name, :store_code, :company_code, :address, :province_id, :district_id, :ward_id, :name, :phone, :email, :image_before, :image_after, :bank_id, :catalogy, :acount_name, :acount_number, :branch_name, :user_add, :time_add, :user_edit, :time_edit, :status, :weight)');

				
				$stmt->bindParam(':user_add', $row['user_add'], PDO::PARAM_INT);
				$stmt->bindParam(':time_add', $row['time_add'], PDO::PARAM_INT);
				$stmt->bindParam(':user_edit', $row['user_edit'], PDO::PARAM_INT);
				$stmt->bindParam(':time_edit', $row['time_edit'], PDO::PARAM_INT);
				$stmt->bindValue(':status', 1, PDO::PARAM_INT);

				$weight = $db->query('SELECT max(weight) FROM ' . $db_config['prefix'] . '_' . $module_data . '_seller_management')->fetchColumn();
				$weight = intval($weight) + 1;
				$stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
			} else {

				$stmt = $db->prepare('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_seller_management SET userid = :userid, description_shop = :description_shop,image_banner = :image_banner,avatar_image = :avatar_image,cover_image = :cover_image,company_name = :company_name, store_code = :store_code, company_code = :company_code, address = :address, province_id = :province_id, district_id = :district_id, ward_id = :ward_id, name = :name, phone = :phone, email = :email, image_before = :image_before, image_after = :image_after, bank_id = :bank_id, catalogy =:catalogy, acount_name = :acount_name, acount_number = :acount_number, branch_name = :branch_name WHERE id=' . $row['id']);
			}
			$stmt->bindParam(':userid', $row['userid'], PDO::PARAM_INT);
			$stmt->bindParam(':description_shop', $row['description_shop'], PDO::PARAM_STR);
			$stmt->bindParam(':image_banner', $row['image_banner'], PDO::PARAM_STR);
			$stmt->bindParam(':avatar_image', $row['avatar_image'], PDO::PARAM_STR);
			$stmt->bindParam(':cover_image', $row['cover_image'], PDO::PARAM_STR);
			$stmt->bindParam(':company_name', $row['company_name'], PDO::PARAM_STR);
			$stmt->bindParam(':store_code', $row['store_code'], PDO::PARAM_STR);
			$stmt->bindParam(':company_code', $row['company_code'], PDO::PARAM_STR);
			$stmt->bindParam(':address', $row['address_shop'], PDO::PARAM_STR);
			$stmt->bindParam(':province_id', $row['province_id_shop'], PDO::PARAM_INT);
			$stmt->bindParam(':district_id', $row['district_id_shop'], PDO::PARAM_INT);
			$stmt->bindParam(':ward_id', $row['ward_id_shop'], PDO::PARAM_INT);
			$stmt->bindParam(':name', $row['name'], PDO::PARAM_STR);
			$stmt->bindParam(':phone', $row['phone_representative'], PDO::PARAM_STR);
			$stmt->bindParam(':email', $row['email_representative'], PDO::PARAM_STR);
			$stmt->bindParam(':image_before', $row['image_before'], PDO::PARAM_STR);
			$stmt->bindParam(':image_after', $row['image_after'], PDO::PARAM_STR);
			$stmt->bindParam(':bank_id', $row['bank_id'], PDO::PARAM_INT);
			$stmt->bindParam(':catalogy', $row['catalogy'], PDO::PARAM_INT);
			$stmt->bindParam(':acount_name', $row['acount_name'], PDO::PARAM_STR);
			$stmt->bindParam(':acount_number', $row['acount_number'], PDO::PARAM_STR);
			$stmt->bindParam(':branch_name', $row['branch_name'], PDO::PARAM_STR);

			$exc = $stmt->execute();
			if ($exc) {
				$district_id_ghn = $global_district[$row['district_id']]['ghnid'];
				$ward_id_ghn = $global_ward[$row['ward_id']]['ghnid'];
				$ward_id_vtp = $global_ward[$row['ward_id']]['vtpid'];
				
				if (empty($row['id'])) {
					$email_contents = 'Gian hàng của bạn đã được tạo thành công, tên đăng nhập: ' . $row['username'] . ', Mật khẩu: ' . $row['password1'];
					$email_title = "Thư báo tạo gian hàng thành công";
					nv_sendmail(array($global_config['site_name'], $global_config['site_email']), $row['email'], sprintf($email_title, $info_order['order_code']), $email_contents);
				}
				$nv_Cache->delMod($module_name);
				if (empty($row['id'])) {
					$sell_id = $db->query('SELECT max(id) FROM ' . $db_config['prefix'] . '_' . $module_data . '_seller_management')->fetchColumn();

					$shops_id_ghn = 0;
					$groupaddressId = 0;
					$cusId = 0;

					$sell_userid = $db->query('SELECT userid FROM ' . $db_config['prefix'] . '_' . $module_data . '_seller_management WHERE id =' . $sell_id)->fetchColumn();

					$sql = "INSERT INTO " . $db_config['prefix'] . '_' . $module_data . "_warehouse_address
							( sell_id, sell_userid, name_warehouse, name_send, phone_send, address, province_id, district_id, ward_id, user_add, time_add, status, shops_id_ghn, lat, lng, groupaddressId, cusId, centerlat, centerlng, maps_mapzoom)
							VALUES
							(:sell_id, :sell_userid, :name_warehouse, :name_send, :phone_send, :address, :province_id, :district_id, :ward_id, :user_add, :time_add, :status, :shops_id_ghn, :lat, :lng, :groupaddressId, :cusId, :centerlat, :centerlng, :maps_mapzoom)";
					$data_insert = array();
					$data_insert['sell_id'] = $sell_id;
					$data_insert['sell_userid'] = $sell_userid;
					$data_insert['name_warehouse'] = $row['name_warehouse'];
					$data_insert['name_send'] = $row['name_send'];
					$data_insert['phone_send'] = $row['phone_send'];
					$data_insert['address'] = $row['address'];
					$data_insert['province_id'] = $row['province_id'];
					$data_insert['district_id'] = $row['district_id'];
					$data_insert['ward_id'] = $row['ward_id'];
					$data_insert['user_add'] = $admin_info['userid'];
					$data_insert['time_add'] = NV_CURRENTTIME;
					$data_insert['status'] = 1;
					$data_insert['shops_id_ghn'] = $shops_id_ghn;
					$data_insert['lat'] = $row['lat'];
					$data_insert['lng'] = $row['lng'];
					$data_insert['groupaddressId'] = $groupaddressId;
					$data_insert['cusId'] = $cusId;
					$data_insert['centerlat'] = $row['centerlat'];
					$data_insert['centerlng'] = $row['centerlng'];
					$data_insert['maps_mapzoom'] = $row['maps_mapzoom'];

					nv_insert_logs(NV_LANG_DATA, $module_name, 'Add Seller_management', ' ', $admin_info['userid']);
				} else {

					
					
					$groupaddressId = 0;
					$cusId = 0;
					$shops_id_ghn = 0;
					//update kho và đơn vị vc
					if ($row['warehouse_id'] > 0) {
						
					}

					nv_insert_logs(NV_LANG_DATA, $module_name, 'Edit Seller_management', 'ID: ' . $row['id'], $admin_info['userid']);
				}
				nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=seller_management');
			}
		} catch (PDOException $e) {
			trigger_error($e->getMessage());
			die($e->getMessage()); //Remove this line after checks finished
		}
	} else {
		
	}
} elseif ($row['id'] > 0) {
	$row = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_seller_management WHERE id=' . $row['id'])->fetch();
	$info = $db->query('SELECT * FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . $row['userid'])->fetch();
	$row['username'] = $info['username'];
	$row['phone_representative'] = $row['phone'];
	$row['email_representative'] = $row['email'];
	$row['email'] = $info['email'];
	$row['phone'] = $info['phone'];
	$row['password'] = '';
	//$row['province_name'] = get_info_province($row['province_id'])['title'];
	//$row['district_name'] = get_info_district($row['district_id'])['title'];
	//$row['ward_name'] = get_info_ward($row['ward_id'])['title'];
	//
	$row['store'] = $db->query("SELECT * FROM " . $db_config['prefix'] . '_' . $module_data . "_warehouse where user_id=" . $row['id'])->fetch();

	if (empty($row)) {
		nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
	}
} else {
	$row['id'] = 0;
	$row['company_name'] = '';
	$row['company_code'] = '';
	$row['store_code'] = '';
	$row['address'] = '';
	$row['province_id'] = 0;
	$row['district_id'] = 0;
	$row['ward_id'] = 0;
	$row['name'] = '';
	$row['phone'] = '';
	$row['email'] = '';
	$row['image_before'] = '';
	$row['image_banner'] = '';
	$row['image_after'] = '';
	$row['bank_id'] = 0;
	$row['acount_name'] = '';
	$row['acount_number'] = '';
	$row['branch_name'] = '';
	$row['store'] = '';
	$row['username'] = '';
	$row['password'] = '';
	$row['catalogy'] = 0;
	
}



$array_bank_id_shops = array();
 $_sql = 'SELECT bank_id,name_bank,bank_code FROM ' . $db_config['prefix'] . '_' . $module_data . '_bank';
$_query = $db->query($_sql);
while ($_row = $_query->fetch()) {
	$array_bank_id_shops[$_row['bank_id']] = $_row;
} 

$xtpl = new XTemplate('seller_management_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
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
$filtersql = ' userid NOT IN (SELECT admin_id FROM ' . NV_AUTHORS_GLOBALTABLE . ') AND userid NOT IN (SELECT userid FROM ' . $db_config['prefix'] . '_' . $module_data . '_seller_management )';
$xtpl->assign('FILTERSQL', $crypt->encrypt($filtersql, NV_CHECK_SESSION));
$xtpl->assign('validate_phone', '[0-9]{10}');
if ($row['id'] > 0) {
	$check = 'readonly';
} else {
	$check = '';
}
$xtpl->assign('check', $check);

// danh sách ngành hàng
foreach($global_array_shops_cat as $catalogy)
{
	$xtpl->assign('OPTION', array(
		'key' => $catalogy['catid'],
		'title' => $catalogy['title'],
		'selected' => ($catalogy['catid'] == $row['catalogy']) ? ' selected="selected"' : ''
	));
	$xtpl->parse('main.catalogy');
}

foreach ($array_bank_id_shops as $value) {
	$xtpl->assign('OPTION', array(
		'key' => $value['bank_id'],
		'title' => $value['bank_code'] . ' - ' . $value['name_bank'],
		'selected' => ($value['bank_id'] == $row['bank_id']) ? ' selected="selected"' : ''
	));
	$xtpl->parse('main.select_bank_id');
}

$row['image_banner'] = explode(',', $row['image_banner']);
$aa = 1;
foreach ($row['image_banner'] as $key => $value) {
	$xtpl->assign('IMG_BANNER_ID', $aa);
	$xtpl->assign('IMG_BANNER', $value);
	$xtpl->parse('main.image_banner');
	$aa++;
}
if ($row['id'] > 0) {
	$xtpl->parse('main.edit');
	$xtpl->parse('main.editjs');

	//$info = get_info_warehouse_store($row['id']);
	
	$info['warehouse_id'] = $info['id'];
	//$info['province_name'] = get_info_province($info['province_id'])['title'];
	//$info['district_name'] = get_info_district($info['district_id'])['title'];
	//$info['ward_name'] = get_info_ward($info['ward_id'])['title'];
	
	$address_ward = explode(',', $info['address']);
	$info['address'] = $address_ward[0];
	$xtpl->assign('AD', $address_ward);

	$xtpl->assign('ROW1', $info);

	$xtpl->parse('main.edit_warehouse');
} else {

		$xtpl->parse('main.add_warehouse_js');
		$xtpl->parse('main.add_warehouse');
	

	$xtpl->parse('main.noedit2');
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
