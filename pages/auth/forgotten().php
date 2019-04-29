<?php
if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;
  $username = $data['username'];
  $token = NoPassAuth::token($username);
  $errors['username'] = 'Email too recently used';
  if (!Cache::get('AuthForgotten_mailto_'.$username)) {
    Cache::set('AuthForgotten_mailto_'.$username,'1',NoPassAuth::$tokenValidity);
    if ($token) {
      mail($username,'Login to '.Router::getBaseUrl(),'Click here: '.Router::getBaseUrl()."auth/reset/$token");
    }
} else {
    $data = array('username' => '');
}