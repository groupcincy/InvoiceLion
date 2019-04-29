<?php

$id += 0;
$reminder += 0;

$tenant = DB::selectOne('select * from `tenants` WHERE `tenants`.`id` = ?', $_SESSION['user']['tenant_id']);
$invoice = DB::selectOne('select * from `invoices` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $id);
$customer = DB::selectOne('select * from `customers` WHERE `tenant_id` = ? AND `id` = ?', $_SESSION['user']['tenant_id'], $invoice['invoices']['customer_id']);
$template = DB::selectOne('select * from `templates` WHERE `tenant_id` = ? AND language_id = ?', $_SESSION['user']['tenant_id'], $customer['customers']['language_id']);
$invoicelines = DB::select('select * from `invoicelines` where `tenant_id` = ? AND `invoice_id` = ?', $_SESSION['user']['tenant_id'], $id);

// calculate multiLine and set reminder
$invoice['invoices']['reminder'] = $reminder;
$invoice['invoices']['multiLine'] = count($invoicelines) > 1;

$data = array(
    'company' => $tenant['tenants'],
    'invoice' => $invoice['invoices'],
    'customer' => $customer['customers'],
    'lines' => array_map(function ($v) {return $v['invoicelines'];}, $invoicelines),
    'now' => date('Y-m-d'),
);

Buffer::set('invoice_styles', $template['templates']['invoice_styles']);
Buffer::set('invoice_template', InvoiceTemplate::render($template['templates']['invoice_template'], $data));
