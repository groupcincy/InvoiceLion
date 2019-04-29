<?php 
if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;
	if (!filter_var($data['users']['username'], FILTER_VALIDATE_EMAIL)) $errors['users[username]'] = "Username is not a valid email address";
	if (!$data['users']['name']) $data['users']['name']=NULL;
	if (!isset($errors)) {
		try {
			$id = DB::insert('INSERT INTO `users` (`tenant_id`, `username`, `password`, `created`, `name`) VALUES (?, ?, ?, ?, ?)', $_SESSION['user']['tenant_id'], $data['users']['username'], '', date('Y-m-d'), $data['users']['name']);
			if ($id) {
				Flash::set('success','User saved');
				Router::redirect('users/index');
			}
			$error = 'User not saved';
		} catch (DBError $e) {
			$error = 'User not saved: '.$e->getMessage();
		}
	}
} else {
	$data = array('users'=>array('username'=>NULL, 'name'=>NULL));
}