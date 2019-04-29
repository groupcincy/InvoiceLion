<?php

$results = array(
    'Hours: hours worked * hourly fee = subtotal'=>DB::selectValues('select id from hours where tenant_id=? and ROUND(subtotal - (hours_worked * hourly_fee))<>0', $_SESSION['user']['tenant_id']),
    'Hours: hours.subtotal = invoicelines.subtotal'=>DB::selectValues('select hours.id from hours, invoicelines where hours.tenant_id=? and hours.invoiceline_id = invoicelines.id and hours.subtotal <> invoicelines.subtotal', $_SESSION['user']['tenant_id']),
    'Hours: hours.tax_percentage = invoicelines.tax_percentage'=>DB::selectValues('select hours.id from hours, invoicelines where hours.tenant_id=? and hours.invoiceline_id = invoicelines.id and hours.tax_percentage <> invoicelines.tax_percentage', $_SESSION['user']['tenant_id']),
    'Invoice: invoices.subtotal = sum(invoicelines.subtotal)'=>DB::selectValues('select invoices.id, invoices.subtotal, sum(invoicelines.subtotal) as invoicelinessum from invoices, invoicelines where invoices.tenant_id=? and invoicelines.invoice_id = invoices.id group by invoices.id having invoices.subtotal <> invoicelinessum', $_SESSION['user']['tenant_id']),
);