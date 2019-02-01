<?php 
if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;
	if (!isset($errors)) {
		try {
			$rowsAffected = DB::update('UPDATE `tenants` SET `payment_period`=?, `reminder_period`=?, `invoice_email`=?, `invoice_styles`=?, `invoice_template`=?, `invoice_page_number`=? WHERE `id` = ?', $data['tenants']['payment_period'], $data['tenants']['reminder_period'], $data['tenants']['invoice_email'], $data['tenants']['invoice_styles'], $data['tenants']['invoice_template'], $data['tenants']['invoice_page_number'], $_SESSION['user']['tenant_id']);
			if ($rowsAffected!==false) {
				Flash::set('success','Invoice settings saved');
				Router::redirect('settings/invoice/view');
			}
			$error = 'Invoice settings not saved';
		} catch (DBError $e) {
			$error = 'Invoice settings not saved: '.$e->getMessage();
		}
	}
} else {
	$data = DB::selectOne('SELECT * from `tenants` WHERE `id` = ?', $_SESSION['user']['tenant_id']);
	$data['tenants']['timetracking']=1;
}