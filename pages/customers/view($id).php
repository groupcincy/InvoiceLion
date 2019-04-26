<?php 
$data = DB::selectOne('select * from `customers` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
$languages = DB::selectPairs('SELECT `id`,`name` FROM `languages`'); // tenant_id not required
$invoices = DB::select('select * from `invoices` where `tenant_id` = ? AND `customer_id`=? order by number desc', $_SESSION['user']['tenant_id'], $id);
