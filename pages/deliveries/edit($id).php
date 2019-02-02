<?php 
$projects = DB::select('select `id`,`name`,`customer_id` from `projects` WHERE `tenant_id` = ? and `active` ORDER BY name', $_SESSION['user']['tenant_id']);
$customers = DB::selectPairs('select `id`,`name` from `customers` WHERE `tenant_id` = ? ORDER BY name', $_SESSION['user']['tenant_id']);

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;

	if (!$data['deliveries']['project_id']) $data['deliveries']['project_id']=NULL;
	if (!$data['deliveries']['comment']) $data['deliveries']['comment']=NULL;
	if (!isset($data['deliveries']['date'])) $errors['deliveries[date]']='Date not set';

	//set vat_percentage to NULL if the customer has vat_reverse_charge
	$vat_reverse_charge = DB::selectValue('select `vat_reverse_charge` from `customers` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $data['deliveries']['customer_id']);
	if ($vat_reverse_charge) $data['deliveries']['vat_percentage']=NULL;

	if (!$data['deliveries']['project_id']) $data['deliveries']['project_id']=NULL;
	if (!$data['deliveries']['comment']) $data['deliveries']['comment']=NULL;
	if (!$data['deliveries']['date']) $errors['deliveries[date]']='Date not set';	
	if (!$data['deliveries']['subtotal']) $errors['deliveries[subtotal]']='Subtotal not set';	
	if (!$data['deliveries']['vat_percentage'] && !$vat_reverse_charge) $errors['deliveries[vat_percentage]']='VAT percentage not set';	
	if (!$data['deliveries']['customer_id']) $errors['deliveries[customer_id]']='Customer not set';	

	if (!isset($errors)) {
		try {
			$rowsAffected = DB::update('UPDATE `deliveries` SET `customer_id`=?, `project_id`=?, `date`=?, `name`=?, `subtotal`=?, `vat_percentage`=?, `comment`=? WHERE `tenant_id` = ? AND `id` = ?', $data['deliveries']['customer_id'], $data['deliveries']['project_id'], $data['deliveries']['date'], $data['deliveries']['name'], $data['deliveries']['subtotal'], $data['deliveries']['vat_percentage'], $data['deliveries']['comment'], $_SESSION['user']['tenant_id'], $id);			

			if ($rowsAffected!==false) {
				Flash::set('success','Delivery saved');
				Router::redirect('deliveries/view/'.$id);
			}
			$error = 'Delivery not saved';
		} catch (DBError $e) {
			$error = 'Delivery not saved: '.$e->getMessage();
		}
	}	
} else {
	$data = DB::selectOne('SELECT * from `deliveries` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
}