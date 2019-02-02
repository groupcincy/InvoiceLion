<?php 
$customers = DB::select('select customers.id,customers.name,count(invoicelines.id) as number_of_invoicelines FROM customers, invoicelines WHERE invoicelines.invoice_id IS NULL AND customers.`tenant_id` = ? AND customers.id = invoicelines.customer_id group by customers.id', $_SESSION['user']['tenant_id']);

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;
	Router::redirect('invoices/invoice/'.$data['invoices']['customer_id']);
}