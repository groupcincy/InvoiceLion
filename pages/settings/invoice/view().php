<?php 
$data = DB::selectOne('SELECT * from `tenants` WHERE `id` = ?', $_SESSION['user']['tenant_id']);
$projects = DB::select('select `id`,`name`,`customer_id` from `projects` WHERE `tenant_id` = ? and `active` ORDER BY name', $_SESSION['user']['tenant_id']);
$hourtypes = DB::select('select * from `hourtypes` WHERE `tenant_id` = ?', $_SESSION['user']['tenant_id']);
$subscriptiontypes = DB::select('select * from `subscriptiontypes` WHERE `tenant_id` = ?', $_SESSION['user']['tenant_id']);
$data['tenants']['timetracking']=1;
$languages = DB::select('SELECT * FROM `languages`'); // tenant_id not required