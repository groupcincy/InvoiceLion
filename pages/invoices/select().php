<?php 
$hours_customer_ids = DB::selectValues('select distinct customer_id from hours where invoiceline_id IS NULL AND `tenant_id` = ?', $_SESSION['user']['tenant_id']);
$deliveries_customer_ids = DB::selectValues('select distinct customer_id from deliveries where invoiceline_id IS NULL AND `tenant_id` = ?', $_SESSION['user']['tenant_id']);
$subscriptionperiods_customer_ids = DB::selectValues('select distinct s.customer_id FROM subscriptions s, subscriptionperiods p WHERE p.subscription_id = s.id AND p.invoiceline_id IS NULL AND s.tenant_id = ?', $_SESSION['user']['tenant_id']);
$customer_ids = array_unique(array_merge($hours_customer_ids, $deliveries_customer_ids, $subscriptionperiods_customer_ids));
if ($customer_ids) $customers = DB::select('select * FROM customers WHERE id IN ('.implode(',',$customer_ids).') AND tenant_id = ?', $_SESSION['user']['tenant_id']);
else $customers = array();

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;
	Router::redirect('invoices/add/'.$data['invoices']['customer_id']);
}