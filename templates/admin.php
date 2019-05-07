<?php
$flash = Flash::get();
$tenant = DB::selectOne('select * from `tenants` WHERE `id` = ?', $_SESSION['user']['tenant_id']);
$currency = DB::selectOne('select * from `currencies` WHERE `id` = ?', $tenant['tenants']['currency_id']);