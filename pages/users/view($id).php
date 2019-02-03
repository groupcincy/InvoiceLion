<?php 
$data = DB::selectOne('select * from `users` WHERE (? = 1 OR `tenant_id` = ?) AND `id` = ?', $_SESSION['user']['superadmin'], $_SESSION['user']['tenant_id'], $id);
