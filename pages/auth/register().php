<?php
$countries = DB::selectPairs('SELECT `id`,`name` FROM `countries`'); // tenant_id not required
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST;
    $countryId = $data['country_id'];
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
            $tenantId = DB::insert('INSERT INTO `tenants` (`name`, `email`, `invoice_email`, `country_id`) VALUES (?, ?, ?, ?)', $username, $username, $username, $countryId); // does not need tenant_id check
            $languages = DB::selectPairs('SELECT `id`, `code` FROM `languages`'); // tenant_id not required
            foreach ($languages as $languageId => $languageCode) {
                DB::insert('INSERT INTO `templates` (`tenant_id`, `language_id`) VALUES (?, ?)', $tenantId, $languageId);
                foreach (array('invoice_styles', 'invoice_template', 'invoiceline_template', 'invoice_page_number') as $field) {
                    DB::update('UPDATE `templates` SET `' . $field . '` = ? WHERE `tenant_id` = ?', file_get_contents("translations/$languageCode/$field.txt"), $tenantId);
                }
                $defaultTaxPercentage = DB::selectValue('SELECT `default_tax_percentage` FROM `countries` WHERE `id` = ?', $countryId); // tenant_id not required
                DB::update('UPDATE `tenants` SET `default_tax_percentage` = ? WHERE `tenant_id` = ?', $defaultTaxPercentage, $tenantId);
            }
            if ($tenantId) {
                $rowsAffected = DB::update('UPDATE `users` SET `tenant_id`=? WHERE `id` = ?', $tenantId, $userId);
                if ($rowsAffected) {
                    $token = NoPassAuth::token($username);
                    if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
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
    $data = array('username' => '', 'country_id' => 1);
}
