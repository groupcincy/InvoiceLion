<?php 
if (!empty($_POST)) {
	$hour = DB::selectOne('select * from `deliveries` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
	try {
		$rows = DB::delete('DELETE FROM `deliveries` WHERE `invoiceline_id` IS NULL AND `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
		
		if ($rows) {
			Flash::set('success','Delivery deleted');
			Router::redirect('deliveries/index');
		}
		$error = 'Delivery not deleted';
	} catch (DBError $e) {
		$error = 'Delivery not deleted: '.$e->getMessage();
	}
}