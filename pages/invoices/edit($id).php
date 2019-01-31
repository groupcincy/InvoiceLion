<?php 
$customers = DB::selectPairs('select `id`,`name` from `customers` WHERE `tenant_id` = ? order by name', $_SESSION['user']['tenant_id']);
$invoice = DB::selectOne('select * from `invoices` WHERE `tenant_id` = ? AND id = ?', $_SESSION['user']['tenant_id'], $id);
$invoicelines = DB::select('select * FROM `invoicelines` WHERE (invoice_id IS NULL OR invoice_id=?) AND `customer_id` = ? AND `tenant_id` = ?', $id, $invoice['invoices']['customer_id'], $_SESSION['user']['tenant_id']);
foreach($invoicelines as $key=>$invoiceline) {
	$invoicelines[$key]['invoicelines']['selected']=($invoiceline['invoicelines']['invoice_id']==$id);
}

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;

	if(count($data['invoiceline_id'])==0) $errors['invoiceline_id'] = 'No invoice line(s) selected';
	if(!isset($data['invoices']['sent']) || !$data['invoices']['sent']) $data['invoices']['sent'] = 0;
	if(!isset($data['invoices']['paid']) || !$data['invoices']['paid']) $data['invoices']['paid'] = 0;
	if(!isset($data['invoices']['reminder1']) || !$data['invoices']['reminder1']) $data['invoices']['reminder1'] = NULL;
	if(!isset($data['invoices']['reminder2']) || !$data['invoices']['reminder2']) $data['invoices']['reminder2'] = NULL;

	if (!isset($customers[$invoice['invoices']['customer_id']])) $errors['invoices[customer_id]']='Customer not found';

	if (!isset($errors)) {
		try {
			//disconnect all invoicelines from this invoice
			foreach ($invoicelines as $invoiceline) $rowsAffected = DB::update('UPDATE `invoicelines` SET `invoice_id`= NULL WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $invoiceline['invoicelines']['id']);

			//santize selected invoiceline ids from checkboxes
			foreach ($data['invoiceline_id'] as $key => $value) {
				$data['invoiceline_id'][$key] = intval($value);
			}

			//calculate sum of selected invoicelines
			$sums = DB::selectOne('SELECT SUM(subtotal) as `invoicelines.subtotal`, SUM(vat) as `invoicelines.vat`, SUM(total) as `invoicelines.total` FROM invoicelines WHERE `tenant_id` = ? AND id IN ('.implode(',',$data['invoiceline_id']).')', $_SESSION['user']['tenant_id']);
			d($sums);

			//update invoice with calculated subtotal
			$rowsAffected = DB::update('UPDATE `invoices` SET `name`=?, `date`=?, `sent`=?, `paid`=?, `reminder1`=?, `reminder2`=?, `customer_id`=?,`subtotal`=?,`vat`=?,`total`=? WHERE `tenant_id` = ? AND `id` = ?', $data['invoices']['name'], $data['invoices']['date'], $data['invoices']['sent'], $data['invoices']['paid'], $data['invoices']['reminder1'], $data['invoices']['reminder2'], $invoice['invoices']['customer_id'], $sums['invoicelines']['subtotal'],$sums['invoicelines']['vat'],$sums['invoicelines']['total'], $_SESSION['user']['tenant_id'], $id);
			
			//connect selected invoicelines to this invoice
			$rowsAffected = DB::update('UPDATE `invoicelines` SET `invoice_id`=? WHERE `tenant_id` = ? AND id IN ('.implode(',',$data['invoiceline_id']).')', $id, $_SESSION['user']['tenant_id']);
			

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