<?php
$languages = DB::select('SELECT `id`,`name` FROM `languages`'); // tenant_id not required
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST;
    if (!isset($errors)) {
        if (!isset($data['customers']['tax_reverse_charge'])) {
            $data['customers']['tax_reverse_charge'] = 0;
        }
        try {
            $rowsAffected = DB::update('UPDATE `customers` SET `name`=?, `email`=?, `contact`=?, `address`=?, `tax_reverse_charge`=?, `language_id`=? WHERE `tenant_id` = ? AND `id` = ?', $data['customers']['name'], $data['customers']['email'], $data['customers']['contact'], $data['customers']['address'], $data['customers']['tax_reverse_charge'], $data['customers']['language_id'], $_SESSION['user']['tenant_id'], $id);
            if ($rowsAffected !== false) {
                Flash::set('success', 'Customer saved');
                Router::redirect('customers/view/' . $id);
            }
            $error = 'Customer not saved';
        } catch (DBError $e) {
            $error = 'Customer not saved: ' . $e->getMessage();
        }
    }
} else {
    $data = DB::selectOne('SELECT * from `customers` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
}
