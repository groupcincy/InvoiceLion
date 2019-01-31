<?php 
$customers = DB::selectPairs('select `id`,`name` from `customers` WHERE `tenant_id` = ? order by name', $_SESSION['user']['tenant_id']);
$invoice = DB::selectOne('select * from `invoices` WHERE `tenant_id` = ? AND id = ?', $_SESSION['user']['tenant_id'], $id);
$invoicelines = DB::select('select * FROM `invoicelines` WHERE (invoice_id IS NULL OR invoice_id=?) AND `customer_id` = ? AND `tenant_id` = ?', $id, $invoice['invoices']['customer_id'], $_SESSION['user']['tenant_id']);

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;

	if(!isset($data['invoices']['sent']) || !$data['invoices']['sent']) $data['invoices']['sent'] = 0;
	if(!isset($data['invoices']['paid']) || !$data['invoices']['paid']) $data['invoices']['paid'] = 0;
	if(!isset($data['invoices']['reminder1']) || !$data['invoices']['reminder1']) $data['invoices']['reminder1'] = NULL;
	if(!isset($data['invoices']['reminder2']) || !$data['invoices']['reminder2']) $data['invoices']['reminder2'] = NULL;

	if (!isset($customers[$invoice['invoices']['customer_id']])) $errors['invoices[customer_id]']='Customer not found';

	if (!isset($errors)) {
		try {
			$rowsAffected = DB::update('UPDATE `invoices` SET `name`=?, `date`=?, `sent`=?, `paid`=?, `reminder1`=?, `reminder2`=?, `customer_id`=? WHERE `tenant_id` = ? AND `id` = ?', $data['invoices']['name'], $data['invoices']['date'], $data['invoices']['sent'], $data['invoices']['paid'], $data['invoices']['reminder1'], $data['invoices']['reminder2'], $invoice['invoices']['customer_id'], $_SESSION['user']['tenant_id'], $id);
			
			//disconnect all invoicelines from this invoice
			foreach ($invoicelines as $invoiceline) $rowsAffected = DB::update('UPDATE `invoicelines` SET `invoice_id`= NULL WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $invoiceline['invoicelines']['id']);

			//connect selected invoicelines to this invoice
			foreach ($data['invoiceline_id'] as $value) $rowsAffected = DB::update('UPDATE `invoicelines` SET `invoice_id`=? WHERE `tenant_id` = ? AND `id` = ?', $id, $_SESSION['user']['tenant_id'], $value);
			
			if ($rowsAffected!==false) {
				Flash::set('success','Invoices saved');
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