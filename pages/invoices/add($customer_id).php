<?php
if (!$customer_id) {
	Router::redirect('invoices/select');
}

$highest_invoice_number = DB::selectValue('SELECT MAX(number) FROM invoices WHERE `tenant_id` = ?', $_SESSION['user']['tenant_id']);
$hours = DB::select('select * FROM `hours` WHERE invoiceline_id IS NULL AND `customer_id` = ? AND `tenant_id` = ?', $customer_id, $_SESSION['user']['tenant_id']);
$deliveries = DB::select('select * FROM `deliveries` WHERE invoiceline_id IS NULL AND `customer_id` = ? AND `tenant_id` = ?', $customer_id, $_SESSION['user']['tenant_id']);
$subscriptionperiods = DB::select('select * FROM subscriptions, subscriptionperiods WHERE subscriptionperiods.subscription_id = subscriptions.id AND subscriptionperiods.invoiceline_id IS NULL AND subscriptions.customer_id = ? AND subscriptions.tenant_id = ?', $customer_id, $_SESSION['user']['tenant_id']);
$template = DB::selectValue('select `invoiceline_template` from `tenants` WHERE `id` = ?', $_SESSION['user']['tenant_id']);

$invoicelines = array();
foreach ($hours as $row) {
	$subtotal = $row['hours']['hours_worked']*$row['hours']['hourly_fee'];
	$vat_percentage = $row['hours']['vat_percentage'];
	if($vat_percentage) $total = $subtotal*((100+$vat_percentage)/100); 
	else $total = $subtotal;
	$name = InvoiceTemplate::render($template, array('type'=>'hours', 'hours'=>$row['hours']));
	$invoicelines['hours_'.$row['hours']['id']] = array(
		'type'=>'hours',
		'name'=>trim(str_replace("\n"," ",$name)),
		'subtotal'=>$subtotal,
		'vat'=>($total - $subtotal),
		'vat_percentage'=>$vat_percentage,
		'total'=>$total,
	);
}
foreach ($deliveries as $row) {
	$subtotal = $row['deliveries']['subtotal'];
	$vat_percentage = $row['deliveries']['vat_percentage'];
	if($vat_percentage) $total = $subtotal*((100+$vat_percentage)/100); 
	else $total = $subtotal;
	$name = InvoiceTemplate::render($template, array('type'=>'delivery', 'delivery'=>$row['deliveries']));
	$invoicelines['deliveries_'.$row['deliveries']['id']] = array(
		'type'=>'delivery',
		'name'=>trim(str_replace("\n"," ",$name)),
		'subtotal'=>$subtotal,
		'vat'=>($total - $subtotal),
		'vat_percentage'=>$vat_percentage,
		'total'=>$total,
	);
}
foreach ($subscriptionperiods as $row) {
	$subtotal = $row['subscriptions']['fee'];
	$vat_percentage = $row['subscriptions']['vat_percentage'];
	if($vat_percentage) $total = $subtotal*((100+$vat_percentage)/100); 
	else $total = $subtotal;
	$name = InvoiceTemplate::render($template, array('type'=>'subscription', 'subscription'=>$row['subscriptions'], 'subscriptionperiod'=>$row['subscriptionperiods']));
	$invoicelines['subscriptionperiods_'.$row['subscriptionperiods']['id']] = array(
		'type'=>'subscription',
		'name'=>trim(str_replace("\n"," ",$name)),
		'subtotal'=>$subtotal,
		'vat'=>($total - $subtotal),
		'vat_percentage'=>$vat_percentage,
		'total'=>$total,
	);
}

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;
	// set error when no invoicelines are selected
	if (!isset($errors)) {
		$error = 'Invoice not saved';
		try {
			$invoicelines = array_filter($invoicelines, function($key) use ($data){ return in_array($key,$data['invoicelines']); }, ARRAY_FILTER_USE_KEY);

			$sums = array('subtotal'=>0,'vat'=>0,'total'=>0);
			foreach ($invoicelines as $invoiceline) {
				$sums['subtotal'] += $invoiceline['subtotal'];
				$sums['vat'] += $invoiceline['vat'];
				$sums['total'] += $invoiceline['total'];
			}

			//DB::begin();
			$invoice_id = DB::insert('INSERT INTO `invoices` (`tenant_id`, `number`, `name`, `date`, `customer_id`, `subtotal`, `vat`, `total`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', $_SESSION['user']['tenant_id'], ($highest_invoice_number+1), $data['invoices']['name'], date('Y-m-d'), $customer_id, $sums['subtotal'], $sums['vat'], $sums['total']);
			foreach ($invoicelines as $key => $invoiceline) {
				$invoiceline_id = DB::insert('INSERT INTO `invoicelines` (`tenant_id`, `invoice_id`, `type`, `name`, `subtotal`, `vat`, `vat_percentage`, `total`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', $_SESSION['user']['tenant_id'], $invoice_id, $invoiceline['type'], $invoiceline['name'], $invoiceline['subtotal'], $invoiceline['vat'], $invoiceline['vat_percentage'], $invoiceline['total']);
				list($table, $id) = explode('_', $key);
				switch ($table) {
					case 'hours':
						DB::update('UPDATE `hours` SET `invoiceline_id` = ? WHERE `tenant_id` = ? AND `id` = ?', $invoiceline_id, $_SESSION['user']['tenant_id'], $id);
						break;
					case 'deliveries':
						DB::update('UPDATE `deliveries` SET `invoiceline_id` = ? WHERE `tenant_id` = ? AND `id` = ?', $invoiceline_id, $_SESSION['user']['tenant_id'], $id);
						break;
					case 'subscriptionperiods':
						DB::update('UPDATE `subscriptionperiods` SET `invoiceline_id` = ? WHERE `tenant_id` = ? AND `id` = ?', $invoiceline_id, $_SESSION['user']['tenant_id'], $id);
						break;
				}
			}
			//DB::commit();

			if ($invoice_id) {
				Flash::set('success','Invoice saved');
				Router::redirect('invoices/index');
			}
			$error = 'Invoice not saved';
		} catch (DBError $e) {
			//DB::rollback();
			$error = 'Invoice not saved: '.$e->getMessage();
		}
	}
} else {
	$data = array('invoices'=>array('number'=>($highest_invoice_number+1), 'name'=>NULL, 'date'=>Date('Y-m-d'), 'sent'=>NULL, 'paid'=>NULL, 'reminder1'=>NULL, 'reminder2'=>NULL, 'customer_id'=>NULL));
}