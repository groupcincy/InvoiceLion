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