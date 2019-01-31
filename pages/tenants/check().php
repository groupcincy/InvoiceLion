<?php

$results = array(
    'Hours: hours worked * hourly fee = subtotal'=>DB::selectValues('select id from hours where tenant_id=? and round(subtotal - (hours_worked * hourly_fee))==0', $_SESSION['user']['tenant_id']),
    'Hours: hours.subtotal = invoicelines.subtotal'=>DB::selectValues('select hours.id from hours, invoicelines where hours.tenant_id=? and hours.invoiceline_id = invoicelines.id and hours.subtotal <> invoicelines.subtotal', $_SESSION['user']['tenant_id']),
);