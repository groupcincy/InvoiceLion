<?php
$data = DB::selectOne('select * from `subscriptionperiods` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
$subscription = DB::selectOne('select * from `subscriptions` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'],$data['subscriptionperiods']['subscription_id']);

$period = 12/$subscription['subscriptions']['months'];
$modificator = ' +'.$period.' months';
$renewalDate = date('Y-m-d', strtotime($data['subscriptionperiods']['from'] . $modificator));

$subtotal = $subscription['subscriptions']['fee'];
if($data['subscriptionperiods']['vat_percentage']) $total = $subtotal*((100+$data['subscriptionperiods']['vat_percentage'])/100); 
else $total = $subtotal;

$invoiceline_id = DB::insert('INSERT INTO `invoicelines` (`tenant_id`, `customer_id`, `name`, `subtotal`, `vat`, `vat_percentage`, `total`) VALUES (?, ?, ?, ?, ?, ?, ?)', $_SESSION['user']['tenant_id'], $subscription['subscriptions']['customer_id'], $data['subscriptionperiods']['name'], $subtotal, ($total - $subtotal), $data['subscriptionperiods']['vat_percentage'], $total);

$subscriptionperiod_id = DB::insert('INSERT INTO `subscriptionperiods` (`tenant_id`, `from`, `name`, `subscription_id`, `comment`,`invoiceline_id`) VALUES (?, ?, ?, ?, ?, ?)', $_SESSION['user']['tenant_id'], $renewalDate, $data['subscriptionperiods']['name'], $data['subscriptionperiods']['subscription_id'], NULL, $invoiceline_id);


if ($subscriptionperiod_id) {
	Flash::set('success','Abonnementperiode saved');
	Router::redirect('subscriptionperiods/index');
}