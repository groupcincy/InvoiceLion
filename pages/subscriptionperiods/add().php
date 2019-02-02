<?php 
$subscriptions = DB::selectPairs('select `id`,`name` from `subscriptions` WHERE `tenant_id` = ? order by name', $_SESSION['user']['tenant_id']);
$customers = DB::selectPairs('select `id`,`name` from `customers` WHERE `tenant_id` = ? ORDER BY name', $_SESSION['user']['tenant_id']);

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;

	if (!isset($subscriptions[$data['subscriptionperiods']['subscription_id']])) $errors['subscriptionperiods[subscription_id]']='Subscription not found';
	if (!isset($data['subscriptionperiods']['comment'])) $data['subscriptionperiods']['comment']=NULL;

	if (!isset($errors)) {
		try {
			$subscriptionperiod_id = DB::insert('INSERT INTO `subscriptionperiods` (`tenant_id`, `from`, `name`, `subscription_id`, `comment`) VALUES (?, ?, ?, ?, ?)', $_SESSION['user']['tenant_id'], $data['subscriptionperiods']['from'], $data['subscriptionperiods']['name'], $data['subscriptionperiods']['subscription_id'], $data['subscriptionperiods']['comment']);

			if ($subscriptionperiod_id) {
				Flash::set('success','Subscription period saved');
				Router::redirect('subscriptionperiods/index');
			}
			$error = 'Subscription period not saved';
		} catch (DBError $e) {
			$error = 'Subscription period not saved: '.$e->getMessage();
		}
	}
} else {
	$data = array('subscriptionperiods'=>array('from'=>NULL, 'name'=>NULL, 'invoice_id'=>NULL, 'subscription_id'=>NULL, 'comment'=>NULL));
}