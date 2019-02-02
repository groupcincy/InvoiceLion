<?php 
$invoice = DB::selectOne('select * from `invoices` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);

if (!empty($_POST) && !$invoice['invoices']['sent']) {
	$error = 'Invoice not deleted';
	try {
		//delete all invoicelines from this invoice
		DB::delete('DELETE FROM `invoicelines` WHERE `tenant_id` = ? AND `invoice_id` = ?', $_SESSION['user']['tenant_id'], $id);

		$rows = DB::delete('DELETE FROM `invoices` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
		if ($rows) {
			Flash::set('success','Invoice deleted');
			Router::redirect('invoices/index');
		}
	} catch (DBError $e) {
		$error.= ': '.$e->getMessage();
	}
}