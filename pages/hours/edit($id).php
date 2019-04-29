<?php
$projects = DB::select('select `id`,`name`,`customer_id` from `projects` WHERE `tenant_id` = ? and `active` ORDER BY name', $_SESSION['user']['tenant_id']);
$customers = DB::selectPairs('select `id`,`name` from `customers` WHERE `tenant_id` = ? ORDER BY name', $_SESSION['user']['tenant_id']);
$hourtypes = DB::selectPairs('select `id`,`name` from `hourtypes` WHERE `tenant_id` = ?', $_SESSION['user']['tenant_id']);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST;

	//set tax_percentage to NULL if the customer has tax_reverse_charge
	$tax_reverse_charge = DB::selectValue('select `tax_reverse_charge` from `customers` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $data['hours']['customer_id']);
	if ($tax_reverse_charge) $data['hours']['tax_percentage']=NULL;
	
	if (!$data['hours']['project_id']) $data['hours']['project_id']=NULL;
	if (!$data['hours']['comment']) $data['hours']['comment']=NULL;
	if (!$data['hours']['type']) $data['hours']['type']=NULL;
	if (!$data['hours']['date']) $errors['hours[date]']='Date not set';	
	if (!$data['hours']['hours_worked']) $errors['hours[hours_worked]']='Hours worked not set';	
	if (!$data['hours']['hourly_fee']) $data['hours']['hourly_fee']=NULL;
	if (!$data['hours']['tax_percentage'] && !$tax_reverse_charge) $errors['hours[tax_percentage]']='tax percentage not set';	
	if (!$data['hours']['customer_id']) $errors['hours[customer_id]']='Customer not set';	

    if (!isset($errors)) {
        try {
            $subtotal = $data['hours']['hours_worked'] * $data['hours']['hourly_fee'];

            $rowsAffected = DB::update('UPDATE `hours` SET `customer_id`=?, `project_id`=?, `date`=?, `name`=?, `hours_worked`=?, `hourly_fee`=?, `subtotal`=?, `tax_percentage`=?, `type`=?, `comment`=? WHERE invoiceline_id IS NULL AND `tenant_id` = ? AND `id` = ?', $data['hours']['customer_id'], $data['hours']['project_id'], $data['hours']['date'], $data['hours']['name'], $data['hours']['hours_worked'], $data['hours']['hourly_fee'], $subtotal, $data['hours']['tax_percentage'], $data['hours']['type'], $data['hours']['comment'], $_SESSION['user']['tenant_id'], $id);

            if ($rowsAffected !== false) {
                Flash::set('success', 'Hours saved');
                Router::redirect('hours/view/' . $id);
            }
            $error = 'Hours not saved';
        } catch (DBError $e) {
            $error = 'Hours not saved: ' . $e->getMessage();
        }
    }
} else {
    $data = DB::selectOne('SELECT * from `hours` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
}
