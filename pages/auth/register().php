<?php
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $data = $_POST;
    $username = $data['username'];
    if (!$username) {
        $errors['username'] = "Username cannot be empty";
    } elseif (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $errors['username'] = "Username is not a valid email address";
    } elseif (Auth::exists($username)) {
        $errors['username'] = "Username is already taken";
    } else {
        $error = "User can not be registered";
        $userId = NoPassAuth::register($username);
        if ($userId) {
            $tenantId = DB::insert('INSERT INTO `tenants` (`name`, `email`, `invoice_email`) VALUES (?, ?, ?)', $username, $username, $username); // does not need tenant_id check
            if ($tenantId) {
                $rowsAffected = DB::update('UPDATE `users` SET `tenant_id`=? WHERE `id` = ?', $tenantId, $userId);
                if ($rowsAffected) {
                    $token = NoPassAuth::token($username);
                    if($_SERVER['REMOTE_ADDR']=='127.0.0.1'){
                        Router::redirect("auth/reset/$token");    
                    }
                    if (!Cache::get('AuthForgotten_mailto_' . $username)) {
                        Cache::set('AuthForgotten_mailto_' . $username, '1', NoPassAuth::$tokenValidity);
                        mail($username, 'Verify email ' . Router::getBaseUrl(), 'Click here: ' . Router::getBaseUrl() . "auth/reset/$token");
                    }
                    Router::redirect("auth/sent");
                }
            }
        }
    }
} else {
    $data = array('username' => '');
}
