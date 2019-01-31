<?php 
if(!$customer_id) $customers = DB::select('select customers.id,customers.name,count(invoicelines.id) as number_of_invoicelines FROM customers, invoicelines WHERE invoicelines.invoice_id IS NULL AND customers.`tenant_id` = ? AND customers.id = invoicelines.customer_id group by customers.id', $_SESSION['user']['tenant_id']);
else {
	$highest_invoice_number = DB::selectValue('SELECT MAX(number) FROM invoices WHERE `tenant_id` = ?', $_SESSION['user']['tenant_id']);
	$invoicelines = DB::select('select * FROM `invoicelines` WHERE invoice_id IS NULL AND `customer_id` = ? AND `tenant_id` = ?', $customer_id, $_SESSION['user']['tenant_id']);
}

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;

	if(isset($data['invoices']['customer_id'])) Router::redirect('invoices/add/'.$data['invoices']['customer_id']);
	
	if(!isset($data['invoices']['sent']) || !$data['invoices']['sent']) $data['invoices']['sent'] = 0;
	if(!isset($data['invoices']['paid']) || !$data['invoices']['paid']) $data['invoices']['paid'] = 0;
	if(!isset($data['invoices']['reminder1']) || !$data['invoices']['reminder1']) $data['invoices']['reminder1'] = NULL;
	if(!isset($data['invoices']['reminder2']) || !$data['invoices']['reminder2']) $data['invoices']['reminder2'] = NULL;

	if (!isset($errors)) {
		$error = 'Invoice not saved';
		try {
			$invoice_id = DB::insert('INSERT INTO `invoices` (`tenant_id`, `number`, `name`, `date`, `sent`, `paid`, `reminder1`, `reminder2`, `customer_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', $_SESSION['user']['tenant_id'], ($highest_invoice_number+1), $data['invoices']['name'], $data['invoices']['date'], $data['invoices']['sent'], $data['invoices']['paid'], $data['invoices']['reminder1'], $data['invoices']['reminder2'], $customer_id);
			
			//connect selected invoicelines to this invoice
			foreach ($data['invoiceline_id'] as $value) $rowsAffected = DB::update('UPDATE `invoicelines` SET `invoice_id`=? WHERE `tenant_id` = ? AND `id` = ?', $invoice_id, $_SESSION['user']['tenant_id'], $value);

			if ($invoice_id) {
				Flash::set('success','Invoice saved');
				Router::redirect('invoices/index');
			}
		} catch (DBError $e) {
			$error.= ': '.$e->getMessage();
		}
	}
} else {
	if($customer_id) $data = array('invoices'=>array('number'=>($highest_invoice_number+1), 'name'=>NULL, 'date'=>Date('Y-m-d'), 'sent'=>NULL, 'paid'=>NULL, 'reminder1'=>NULL, 'reminder2'=>NULL, 'customer_id'=>NULL));
}