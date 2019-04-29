<?php 
if(!isset($order) || !in_array($order,['id','number','name','date','sent','paid','reminder1','reminder2','customer'])) $order = 'number';
if(!isset($asc) || !in_array($asc,['asc','desc'])) $asc = 'desc';

if($order == 'customer') $data = DB::select('select * from `invoices`,`customers` WHERE `invoices`.`tenant_id` = ? AND `invoices`.customer_id = `customers`.id order by `customers`.name '.$asc, $_SESSION['user']['tenant_id']);
else $data = DB::select('select * from `invoices` WHERE `tenant_id` = ? order by '.$order.' '.$asc, $_SESSION['user']['tenant_id']);

$customers = DB::selectPairs('select `id`,`name` from `customers` WHERE `tenant_id` = ?', $_SESSION['user']['tenant_id']);