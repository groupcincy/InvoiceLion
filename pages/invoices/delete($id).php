<?php 
$invoice = DB::selectOne('select * from `invoices` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
$invoicelines = DB::select('select * FROM `invoicelines` WHERE invoice_id=? AND `tenant_id` = ?', $id, $_SESSION['user']['tenant_id']);


if (!empty($_POST) && !$invoice['invoices']['sent']) {
	$error = 'Invoice not deleted';
	try {
		//disconnect all invoicelines from this invoice
		foreach ($invoicelines as $invoiceline) $rowsAffected = DB::update('UPDATE `invoicelines` SET `invoice_id`= NULL WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $invoiceline['invoicelines']['id']);

		$rows = DB::delete('DELETE FROM `invoices` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
		if ($rows) {
			Flash::set('success','Invoice deleted');
			Router::redirect('invoices/index');
		}
	} catch (DBError $e) {
		$error.= ': '.$e->getMessage();
	}
}