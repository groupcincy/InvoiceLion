<?php 

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;

	if (!isset($errors)) {
		try {

			$rowsAffected = DB::update('UPDATE `subscriptionperiods` SET `name`=?, `comment`=? WHERE `invoiceline_id` IS NULL AND `tenant_id` = ? AND `id` = ?', $data['subscriptionperiods']['name'], $data['subscriptionperiods']['comment'], $_SESSION['user']['tenant_id'], $id);

			if ($rowsAffected!==false) {
				Flash::set('success','Subscription period saved');
				Router::redirect('subscriptionperiods/view/'.$id);
			}
			$error = 'Subscription period not saved';
		} catch (DBError $e) {
			$error = 'Subscription period not saved: '.$e->getMessage();
		}
	}
} else {
	$data = DB::selectOne('SELECT * from `subscriptionperiods` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
}