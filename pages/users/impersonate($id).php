<?php 
if (!empty($_POST)) {
    $user = DB::selectOne('select * from `users` WHERE (? = 1 OR `tenant_id` = ?) AND `id` = ?', $_SESSION['user']['superadmin'], $_SESSION['user']['tenant_id'], $id);
    if ($user) {
        $_SESSION['user'] = $user['users'];
        Flash::set('success', 'User impersonated');
        Router::redirect('');
    }
    $error = 'User not impersonated';
}