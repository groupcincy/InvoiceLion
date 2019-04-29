<?php 
if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;
	if (!isset($errors)) {
		try {
			$rowsAffected = DB::update('UPDATE `tenants` SET `name`=?, `address`=?, `email`=?, `phone`=?, `bank_account_number`=?, `bank_account_name`=?, `bank_name`=?, `bank_bic`=?, `bank_city`=?, `coc_number`=?, `tax_number`=? WHERE `id` = ?', $data['tenants']['name'], $data['tenants']['address'], $data['tenants']['email'], $data['tenants']['phone'], $data['tenants']['bank_account_number'], $data['tenants']['bank_account_name'], $data['tenants']['bank_name'], $data['tenants']['bank_bic'], $data['tenants']['bank_city'], $data['tenants']['coc_number'], $data['tenants']['tax_number'], $_SESSION['user']['tenant_id']);
			if ($rowsAffected!==false) {
				Flash::set('success','Company settings saved');
				Router::redirect('settings/company/view');
			}
			$error = 'Company settings not saved';
		} catch (DBError $e) {
			$error = 'Company settings not saved: '.$e->getMessage();
		}
	}
} else {
	$data = DB::selectOne('SELECT * from `tenants` WHERE `id` = ?', $_SESSION['user']['tenant_id']);
	$data['tenants']['timetracking']=1;
}