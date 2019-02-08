<?php 

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;
	if(isset($data['invoices']['paid']) && ($data['invoices']['paid'] == '0000-00-00' || $data['invoices']['paid'] == '')) $data['invoices']['paid'] = NULL; 

	if (!isset($errors)) {
		try {
			//update date if not sent
			$rowsAffected = DB::update('UPDATE `invoices` SET `date` = ?, `sent` = ? WHERE `sent` IS NULL AND `tenant_id` = ? AND `id` = ?', $data['invoices']['date'], $data['invoices']['sent'], $_SESSION['user']['tenant_id'], $id);
			//update paid if adjusted (also from sent invoices)
			$rowsAffected = DB::update('UPDATE `invoices` SET `paid` = ? WHERE `tenant_id` = ? AND `id` = ?', $data['invoices']['paid'], $_SESSION['user']['tenant_id'], $id);

			if ($rowsAffected!==false) {
				Flash::set('success','Invoice saved');
				Router::redirect('invoices/view/'.$id);
			}
			$error = 'Invoice not saved';
		} catch (DBError $e) {
			$error = 'Invoice not saved: '.$e->getMessage();
		}
	}
} else {
	$data = DB::selectOne('SELECT * from `invoices` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
}