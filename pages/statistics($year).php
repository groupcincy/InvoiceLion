<?php 
if(!isset($year)) $year = date("Y");

$subscriptionIncome = DB::selectValue('SELECT SUM((fee*(12/months))/12) FROM subscriptions WHERE `tenant_id` = ? AND (canceled IS NULL OR canceled > ?) AND `from` <= ?', $_SESSION['user']['tenant_id'],$year.'-12-31',$year.'-12-31');

$incomeThisYear = DB::selectValue('SELECT sum(subtotal) FROM invoices WHERE `tenant_id` = ? AND `sent` and YEAR(`date`) = ?', $_SESSION['user']['tenant_id'],$year);

if(!isset($year) || $year == date("Y")) {
    $hoursAddon = DB::selectValue('SELECT sum(subtotal) FROM `hours` WHERE `tenant_id` = ? AND `invoiceline_id` IS NULL', $_SESSION['user']['tenant_id']);
    $deliveryAddon = DB::selectValue('SELECT sum(subtotal) FROM `deliveries` WHERE `tenant_id` = ? AND `invoiceline_id` IS NULL', $_SESSION['user']['tenant_id']);
    $incomeThisYearAddon = $hoursAddon + $deliveryAddon;
}

$sumhourspertype_thisyear = DB::selectPairs('SELECT `hourtypes`.name,SUM(hours_worked) as `hourtypes.sum` FROM `hours` LEFT JOIN `hourtypes` ON `hours`.`type` = `hourtypes`.id WHERE `hours`.`tenant_id` = ? AND year(hours.date) = ? GROUP BY `type`', $_SESSION['user']['tenant_id'],$year);
arsort($sumhourspertype_thisyear);
$totalhours_thisyear = array_sum($sumhourspertype_thisyear);


$sumsubscriptionspertype_thisyear = DB::select('SELECT `subscriptiontypes`.id, `subscriptiontypes`.name, count(subscriptions.id) as `subscriptiontypes.times`, SUM(fee/subscriptions.months) as `subscriptiontypes.sum` FROM `subscriptions` LEFT JOIN `subscriptiontypes` ON `subscriptions`.`subscriptiontype_id` = `subscriptiontypes`.id WHERE `subscriptions`.`tenant_id` = ? AND (subscriptions.canceled IS NULL OR subscriptions.canceled > ?) AND subscriptions.from <= ? GROUP BY `subscriptiontype_id`', $_SESSION['user']['tenant_id'],$year.'-12-31',$year.'-12-31');

$subscriptionincome_graph = DB::select('SELECT SUM(invoicelines.`subtotal`) as invoiced, invoices.`date`,  DAYOFYEAR(invoices.`date`)/DAYOFYEAR(?) AS day_number
    FROM invoices, invoicelines
    WHERE invoicelines.tenant_id = ? 
    AND YEAR(invoices.`date`) = ? 
    AND invoicelines.invoice_id = invoices.id 
    AND invoicelines.id IN (SELECT invoiceline_id from subscriptionperiods)
    GROUP BY invoices.`date`
    ORDER BY invoices.`date`',$year."-12-31",$_SESSION['user']['tenant_id'],$year);

$othersincome_graph = DB::select('SELECT SUM(invoicelines.`subtotal`) as invoiced, invoices.`date`,  DAYOFYEAR(invoices.`date`)/DAYOFYEAR(?) AS day_number
    FROM invoices, invoicelines
    WHERE invoicelines.tenant_id = ? 
    AND YEAR(invoices.`date`) = ? 
    AND invoicelines.invoice_id = invoices.id 
    AND (invoicelines.id IN (SELECT invoiceline_id from hours) OR invoicelines.id IN (SELECT invoiceline_id from deliveries))
    GROUP BY invoices.`date`
    ORDER BY invoices.`date`',$year."-12-31",$_SESSION['user']['tenant_id'],$year);

$totalincome_graph = DB::select('SELECT SUM(`subtotal`) as invoiced, `date`,  DAYOFYEAR(`date`)/DAYOFYEAR(?) AS day_number
    FROM invoices
    WHERE tenant_id = ? AND YEAR(`date`) = ?
    GROUP BY `date`
    ORDER BY `date`',$year."-12-31",$_SESSION['user']['tenant_id'],$year);

$totalpaid_graph = DB::select('SELECT SUM(`subtotal`) as invoiced, `paid`,  DAYOFYEAR(`paid`)/DAYOFYEAR(?) AS day_number
    FROM invoices
    WHERE tenant_id = ? AND YEAR(`paid`) = ?
    GROUP BY `paid`
    ORDER BY `paid`',$year."-12-31",$_SESSION['user']['tenant_id'],$year);

$totalwork_graph = DB::select('SELECT SUM(`worked`) as worked, `date`,  DAYOFYEAR(`date`)/DAYOFYEAR(?) AS day_number FROM (
    SELECT SUM(`subtotal`) as worked, `date`,  DAYOFYEAR(`date`)/DAYOFYEAR(?) AS day_number
    FROM `hours`
    WHERE tenant_id = ? AND YEAR(`date`) = ?
    GROUP BY `date`
    
    UNION

    SELECT SUM(`subtotal`) as worked, `date`,  DAYOFYEAR(`date`)/DAYOFYEAR(?) AS day_number
    FROM `deliveries`
    WHERE tenant_id = ? AND YEAR(`date`) = ?
    GROUP BY `date`

    UNION

    SELECT SUM(`fee`) as worked, subscriptionperiods.`from` as `date`,  DAYOFYEAR(subscriptionperiods.`from`)/DAYOFYEAR(?) AS day_number
    FROM `subscriptionperiods`,`subscriptions`
    WHERE subscriptions.tenant_id = ? AND YEAR(subscriptionperiods.`from`) = ? AND subscriptionperiods.subscription_id = subscriptions.id
    GROUP BY subscriptionperiods.`from`
    
    ) as a
    GROUP BY `date`
    ORDER BY `date`    
    ',$year."-12-31",$year."-12-31",$_SESSION['user']['tenant_id'],$year,$year."-12-31",$_SESSION['user']['tenant_id'],$year,$year."-12-31",$_SESSION['user']['tenant_id'],$year);


$maxyearincome = DB::selectValue('SELECT SUM(`subtotal`) AS invoiced, YEAR(`date`) as year_number FROM invoices WHERE tenant_id = ? GROUP BY year_number ORDER BY invoiced DESC LIMIT 1',$_SESSION['user']['tenant_id']);
$maxyearincome = ceil($maxyearincome/10000) * 10000;


function is_leap_year($year) {
	return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0)));
}

$currentday = date('z') + 1;
if($year == date('Y')) {
    if(is_leap_year($year)) $relativeday = $currentday/366;
    else $relativeday = $currentday/365;
} else {
    $relativeday = 1;
}