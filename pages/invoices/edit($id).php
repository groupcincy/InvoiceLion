<?php 
$invoice = DB::selectOne('select * from `invoices` WHERE `tenant_id` = ? AND id = ?', $_SESSION['user']['tenant_id'], $id);

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;

	if (!isset($errors)) {
		try {
			$rowsAffected = DB::update('UPDATE `invoices` SET `paid` = ? WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $invoice['invoices']['id']);

			if ($rowsAffected!==false) {
				Flash::set('success','Invoice saved');
				Router::redirect('invoices/view/'.$id);
			}
			$error = 'Invoice not saved';
		} catch (DBError $e) {
			$error = 'Invoice not saved: '.$e->getMessage();
		}
	}
} else {
	$data = DB::selectOne('SELECT * from `invoices` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
}