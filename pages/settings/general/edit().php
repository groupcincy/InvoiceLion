<?php 
if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;
	if (!isset($errors)) {
		try {
			$rowsAffected = DB::update('UPDATE `tenants` SET `default_vat_percentage`=?, `default_hourly_fee`=?, `subscriptions_active`=?, `hours_active`=? WHERE `id` = ?', $data['tenants']['default_vat_percentage'], $data['tenants']['default_hourly_fee'], $data['tenants']['subscriptions_active'], $data['tenants']['hours_active'], $_SESSION['user']['tenant_id']);
			if ($rowsAffected!==false) {
				Flash::set('success','General settings saved');
				Router::redirect('settings/general/view');
			}
			$error = 'General settings not saved';
		} catch (DBError $e) {
			$error = 'General settings not saved: '.$e->getMessage();
		}
	}
} else {
	$data = DB::selectOne('SELECT * from `tenants` WHERE `id` = ?', $_SESSION['user']['tenant_id']);
	$data['tenants']['timetracking']=1;
}