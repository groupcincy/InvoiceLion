<?php
if (!empty($_POST)) {
    $hour = DB::selectOne('select * from `hours` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
    try {
        $rows = DB::delete('DELETE FROM `hours` WHERE `invoiceline_id` IS NULL AND `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);

        if ($rows) {
            Flash::set('success', 'Hours deleted');
            Router::redirect('hours/index');
        }
        $error = 'Hours not deleted';
    } catch (DBError $e) {
        $error = 'Hours not deleted: ' . $e->getMessage();
    }
}
