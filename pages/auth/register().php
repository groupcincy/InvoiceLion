<?php
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $data = $_POST;
    $country = $data['country'];
    $countries = array("nl");
    if (!in_array($country,$countries)) {
        $errors['country'] = "Invalid country";
    }
    $username = $data['username'];
    if (!$username) {
        $errors['username'] = "Username cannot be empty";
    } elseif (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $errors['username'] = "Username is not a valid email address";
    } elseif (Auth::exists($username)) {
        $errors['username'] = "Username is already taken";
    } 
    if (!isset($errors)) {
        $error = "User can not be registered";
        $userId = NoPassAuth::register($username);
        if ($userId) {
            $tenantId = DB::insert('INSERT INTO `tenants` (`name`, `email`, `invoice_email`, `country`) VALUES (?, ?, ?, ?)', $username, $username, $username, $country); // does not need tenant_id check
            foreach(array('invoice_styles', 'invoice_template', 'invoiceline_template', 'invoice_page_number', 'default_vat_percentage') as $field) {
                DB::update('UPDATE `tenants` SET `'.$field.'` = ? WHERE `id` = ?', file_get_contents("translations/$country/$field.txt"), $tenantId); // does not need tenant_id check
            }
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
    $data = array('username' => '', 'country' => 'nl');
}
