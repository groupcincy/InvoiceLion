<?php 

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;

	if (!isset($errors)) {
		try {
			$rowsAffected = DB::update('UPDATE `invoices` SET `date` = ? WHERE `sent` IS NULL AND `tenant_id` = ? AND `id` = ?', $data['invoices']['date'], $_SESSION['user']['tenant_id'], $id);
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