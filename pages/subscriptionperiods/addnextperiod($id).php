<?php
$subscription = DB::selectOne('select * from `subscriptions` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'],$id);
$subscriptionperiod = DB::selectOne('select * from `subscriptionperiods` WHERE `tenant_id` = ? AND `subscription_id` = ? ORDER BY `from` DESC LIMIT 1', $_SESSION['user']['tenant_id'], $id);

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;

	$period = $subscription['subscriptions']['months'];
	$subscriptionperiod['subscriptionperiods']['from'] = date('Y-m-d', strtotime($subscriptionperiod['subscriptionperiods']['from'] . ' +'.$period.' months'));
	$subscriptionperiod['subscriptionperiods']['until'] = date('Y-m-d', strtotime($subscriptionperiod['subscriptionperiods']['from'] . ' +'.$period.' months - 1 day'));

	try {
		$subscriptionperiod_id = DB::insert('INSERT INTO `subscriptionperiods` (`tenant_id`, `from`, `until`, `name`, `subscription_id`, `comment`) VALUES (?, ?, ?, ?, ?, ?)', $_SESSION['user']['tenant_id'], $subscriptionperiod['subscriptionperiods']['from'], $subscriptionperiod['subscriptionperiods']['until'], $subscriptionperiod['subscriptionperiods']['name'], $subscriptionperiod['subscriptionperiods']['subscription_id'], NULL);

		if ($subscriptionperiod_id) {
			Flash::set('success','Subscription period saved');
			Router::redirect('subscriptions/view/'.$id);
		}
	} catch (DBError $e) {
		$error = 'Subscription period not saved: '.$e->getMessage();
	}
}