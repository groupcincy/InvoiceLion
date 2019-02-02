<?php
$subscriptiontypes = DB::selectPairs('select `id`,`name` from `subscriptiontypes` WHERE `tenant_id` = ?', $_SESSION['user']['tenant_id']);
$projects = DB::select('select `id`,`name`,`customer_id` from `projects` WHERE `tenant_id` = ? and `active` ORDER BY name', $_SESSION['user']['tenant_id']);
$customers = DB::selectPairs('select `id`,`name` from `customers`  WHERE `tenant_id` = ?', $_SESSION['user']['tenant_id']);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST;

    if ($data['subscriptions']['add_customer']) {
		$data['subscriptions']['customer_id'] = DB::insert('INSERT INTO `customers` (`tenant_id`, `name`) VALUES (?, ?)', $_SESSION['user']['tenant_id'], $data['subscriptions']['add_customer']);
		$data['subscriptions']['add_customer'] = null;
		$customers = DB::selectPairs('select `id`,`name` from `customers`  WHERE `tenant_id` = ?', $_SESSION['user']['tenant_id']);
	}

	//set vat_percentage to NULL if the customer has vat_reverse_charge
	$vat_reverse_charge = DB::selectValue('select `vat_reverse_charge` from `customers` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $data['subscriptions']['customer_id']);
	if ($vat_reverse_charge) $data['subscriptions']['vat_percentage']=NULL;

    if (!$data['subscriptions']['from']) $errors['subscriptions[from]'] = 'Date not set';
    if (!$data['subscriptions']['name']) $errors['subscriptions[name]'] = 'Name not set';
    if (!$data['subscriptions']['fee']) $errors['subscriptions[fee]'] = 'Fee not set';
    if (!$data['subscriptions']['months']) $errors['subscriptions[months]'] = 'Subscription period not set';
    if (!$data['subscriptions']['vat_percentage'] && !$vat_reverse_charge) $errors['subscriptions[vat_percentage]']='VAT percentage not set';	
	if (!$data['subscriptions']['project_id']) $data['subscriptions']['project_id'] = null;
    if (!$data['subscriptions']['subscriptiontype_id']) $data['subscriptions']['subscriptiontype_id'] = null;
	if (!$data['subscriptions']['customer_id']) $errors['hours[customer_id]']='Customer not set';	

    if (!isset($errors)) {
        try {
            $subscription_id = DB::insert('INSERT INTO `subscriptions` (`tenant_id`, `fee`, `vat_percentage`, `months`, `name`, `from`, `comment`, `subscriptiontype_id`, `customer_id`, `project_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $_SESSION['user']['tenant_id'], $data['subscriptions']['fee'], $data['subscriptions']['vat_percentage'], $data['subscriptions']['months'], $data['subscriptions']['name'], $data['subscriptions']['from'], $data['subscriptions']['comment'], $data['subscriptions']['subscriptiontype_id'], $data['subscriptions']['customer_id'], $data['subscriptions']['project_id']);
            
            //get the until date
            $until = date('Y-m-d', strtotime($data['subscriptions']['from'] . ' +'.$data['subscriptions']['months'].' months - 1 day'));
            //add first subscriptionperiod
            $subscriptionperiod_id = DB::insert('INSERT INTO `subscriptionperiods` (`tenant_id`, `from`, `until`, `name`, `subscription_id`, `comment`,`invoiceline_id`) VALUES (?, ?, ?, ?, ?, ?, ?)', $_SESSION['user']['tenant_id'], $data['subscriptions']['from'], $until, $data['subscriptions']['name'], $subscription_id, NULL , NULL);

            if ($subscription_id) {
                Flash::set('success', 'Subscription saved');
                Router::redirect('subscriptions/index');
            }
            $error = 'Subscription not saved';
        } catch (DBError $e) {
            $error = 'Subscription not saved: ' . $e->getMessage();
        }
    }
} else {
    $data = array('subscriptions' => array(
        'fee' => null,
        'vat_percentage' => $tenant['tenants']['default_vat_percentage'],
        'months' => null,
        'name' => null,
        'from' => null,
        'canceled' => null,
        'comment' => null,
        'subscriptiontype_id' => null,
        'customer_id' => null,
        'add_customer' => null,
        'project_id' => null,
    ));
}
