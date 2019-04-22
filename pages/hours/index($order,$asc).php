<?php 
if(!isset($order) || !in_array($order,['date','hours_worked','name','subtotal','customer'])) $order = 'date';
if(!isset($asc) || !in_array($asc,['asc','desc'])) $asc = 'desc';

if($order == 'customer') $data = DB::select('select * from `hours`,`customers` WHERE `invoices`.`tenant_id` = ? AND `hours`.customer_id = `customers`.id order by `customers`.name '.$asc, $_SESSION['user']['tenant_id']);
else $data = DB::select('select * from `hours` WHERE `tenant_id` = ? order by `'.$order.'` '.$asc.', `id` '.$asc.' limit 200', $_SESSION['user']['tenant_id']);

$customers = DB::selectPairs('select `id`,`name` from `customers` WHERE `tenant_id` = ?', $_SESSION['user']['tenant_id']);