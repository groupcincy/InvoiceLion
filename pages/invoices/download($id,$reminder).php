<?php

$id+=0;
$reminder+=0;

$tenant = DB::selectOne('select * from `tenants` WHERE `tenants`.`id` = ?', $_SESSION['user']['tenant_id']);
$invoice = DB::selectOne('select * from `invoices` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
$customer = DB::selectOne('select * from `customers` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'],$invoice['invoices']['customer_id']);
$invoicelines = DB::select('select * from `invoicelines` where `tenant_id` = ? AND `invoice_id`=?', $_SESSION['user']['tenant_id'],$id);

// calculate multiLine and set reminder
$invoice['invoices']['reminder'] = $reminder;
$invoice['invoices']['multiLine'] = count($invoicelines)>1;

$data = array(
    'company'=>$tenant['tenants'],
    'invoice'=>$invoice['invoices'],
    'customer'=>$customer['customers'],
    'lines'=>array_map(function($v){return $v['invoicelines'];},$invoicelines),
    'now'=>time(),
);

Buffer::set('invoice_styles',$tenant['tenants']['invoice_styles']);
Buffer::set('invoice_template',InvoiceTemplate::render($tenant['tenants']['invoice_template'], $data));
