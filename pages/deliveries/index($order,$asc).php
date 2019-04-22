<?php 
if(!isset($order) || !in_array($order,['date','name','subtotal','customer'])) $order = 'date';
if(!isset($asc) || !in_array($asc,['asc','desc'])) $asc = 'desc';

if($order == 'customer') $data = DB::select('select * from `deliveries`,`customers` WHERE `deliveries`.`tenant_id` = ? AND `deliveries`.customer_id = `customers`.id order by `customers`.name '.$asc.' limit 200', $_SESSION['user']['tenant_id']);
else $data = DB::select('select * from `deliveries` WHERE `tenant_id` = ? order by `'.$order.'` '.$asc.', `id` '.$asc.' limit 200', $_SESSION['user']['tenant_id']);

$invoices = DB::selectPairs('select `id`,`name` from `invoices` WHERE `tenant_id` = ?', $_SESSION['user']['tenant_id']);
$projects = DB::selectPairs('select `id`,`name` from `projects` WHERE `tenant_id` = ?', $_SESSION['user']['tenant_id']);
$customers = DB::selectPairs('select `id`,`name` from `customers` WHERE `tenant_id` = ?', $_SESSION['user']['tenant_id']);
