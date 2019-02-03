<?php 
$data = DB::select('select * from `users` WHERE (? = 1 OR `tenant_id` = ?)', $_SESSION['user']['superadmin'], $_SESSION['user']['tenant_id']);
