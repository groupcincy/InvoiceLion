<?php
$uninvoiced_hours = DB::select('SELECT * FROM `hours` WHERE `tenant_id` = ? AND invoiceline_id IS NULL AND `subtotal` <> 0.00', $_SESSION['user']['tenant_id']);
$uninvoiced_subscriptions = DB::select('SELECT * FROM subscriptionperiods, subscriptions WHERE subscriptionperiods.tenant_id = ? AND subscriptionperiods.invoiceline_id IS NULL AND subscriptions.id = subscriptionperiods.subscription_id', $_SESSION['user']['tenant_id']);
$uninvoiced_deliveries = DB::select('SELECT * FROM `deliveries` WHERE `tenant_id` = ? AND invoiceline_id IS NULL', $_SESSION['user']['tenant_id']);

$unpaid_invoices = DB::select('SELECT * FROM invoices WHERE `tenant_id` = ? AND `sent` and `paid` IS NULL ORDER BY reminder2,reminder1,`sent` ASC', $_SESSION['user']['tenant_id']);
krsort($unpaid_invoices);

$unsent_invoices = DB::select('SELECT * FROM invoices WHERE `tenant_id` = ? AND (`sent` = "0000-00-00" OR `sent` IS NULL) ORDER BY invoices.number DESC', $_SESSION['user']['tenant_id']);

$unsent_reminders = DB::select('SELECT * FROM invoices WHERE `tenant_id` = ? AND `sent` and `paid` IS NULL AND date < NOW() - INTERVAL 1 MONTH AND reminder1 IS NULL ORDER BY name', $_SESSION['user']['tenant_id']);

$unsent_reminders2 = DB::select('SELECT * FROM invoices WHERE `tenant_id` = ? AND `paid` IS NULL AND reminder1 < NOW() - INTERVAL 2 WEEK AND reminder2 IS NULL ORDER BY name', $_SESSION['user']['tenant_id']);

$missing_period_ids = DB::selectValues('SELECT `subscriptions`.id, ceil(timestampdiff(DAY,`subscriptions`.`from`, if(`subscriptions`.`canceled` is null,now(),`subscriptions`.`canceled`))/((365.25/12)*`subscriptions`.`months`)) as `expected_days`, count(`subscriptionperiods`.id) as `actual_days` from `subscriptions`, `subscriptionperiods` where `subscriptionperiods`.`tenant_id` = ? AND `subscriptionperiods`.`subscription_id` = `subscriptions`.`id` group by `subscriptions`.id having `actual_days` < `expected_days`', $_SESSION['user']['tenant_id']);

if($missing_period_ids) $erronous_subscriptions = DB::select('SELECT * FROM subscriptions WHERE subscriptions.`tenant_id` = ? AND id IN ('.implode(',',$missing_period_ids).')', $_SESSION['user']['tenant_id']);

$hours_no_invoices = DB::select('SELECT `hours`.* FROM `hours`, `invoicelines` WHERE `hours`.`tenant_id` = ? AND `hours`.`invoiceline_id` = `invoicelines`.`id` AND (`invoicelines`.`subtotal` IS NOT NULL and `invoicelines`.`subtotal`<>0) AND `invoicelines`.`invoice_id` IS NULL', $_SESSION['user']['tenant_id']);
