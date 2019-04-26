<?php
$language = DB::selectOne('SELECT * FROM `languages` WHERE `id` = ?', $id); // tenant_id not required
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST;
    if (!isset($errors)) {
        try {
            $rowsAffected = DB::update('UPDATE `templates` SET `invoice_styles`=?, `invoiceline_template`=?, `invoice_template`=?, `invoice_page_number`=? WHERE `tenant_id` = ? AND `language_id` = ?', $data['templates']['invoice_styles'], $data['templates']['invoiceline_template'], $data['templates']['invoice_template'], $data['templates']['invoice_page_number'], $_SESSION['user']['tenant_id'], $language['languages']['id']);
            if ($rowsAffected !== false) {
                Flash::set('success', 'Language settings saved');
                Router::redirect('settings/languages/view/' . $language['languages']['id']);
            }
            $error = 'Invoice settings not saved';
        } catch (DBError $e) {
            $error = 'Invoice settings not saved: ' . $e->getMessage();
        }
    }
} else {
    $data = DB::selectOne('SELECT * FROM `templates` WHERE `tenant_id` = ? AND `language_id` = ?', $_SESSION['user']['tenant_id'], $language['languages']['id']);
}
