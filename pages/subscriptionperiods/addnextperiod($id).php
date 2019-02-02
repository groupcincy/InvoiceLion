<?php
$subscription = DB::selectOne('select * from `subscriptions` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'],$id);
$data = DB::selectOne('select * from `subscriptionperiods` WHERE `tenant_id` = ? AND `subscription_id` = ? ORDER BY from DESC LIMIT 1', $_SESSION['user']['tenant_id'], $id);

$period = $subscription['subscriptions']['months'];
$data['subscriptionperiods']['from'] = date('Y-m-d', strtotime($data['subscriptionperiods']['from'] . ' +'.$period.' months'));
$data['subscriptionperiods']['until'] = date('Y-m-d', strtotime($data['subscriptionperiods']['from'] . ' +'.$period.' months - 1 day'));

$subscriptionperiod_id = DB::insert('INSERT INTO `subscriptionperiods` (`tenant_id`, `from`, `until`, `name`, `subscription_id`, `comment`) VALUES (?, ?, ?, ?, ?, ?)', $_SESSION['user']['tenant_id'], $data['subscriptionperiods']['from'], $data['subscriptionperiods']['until'], $data['subscriptionperiods']['name'], $data['subscriptionperiods']['subscription_id'], NULL);

if ($subscriptionperiod_id) {
	Flash::set('success','Abonnementperiode saved');
	Router::redirect('subscriptionperiods/index');
}