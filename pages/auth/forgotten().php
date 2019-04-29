<?php
if ($_SERVER['REQUEST_METHOD']=='POST') {
	$data = $_POST;
  $username = $data['username'];
  $token = NoPassAuth::token($username);
  $errors['username'] = 'Email too recently used';
  if (!Cache::get('AuthForgotten_mailto_'.$username)) {
    Cache::set('AuthForgotten_mailto_'.$username,'1',NoPassAuth::$tokenValidity);
    if ($token) {
      mail($username,'Login to '.Router::getBaseUrl(),'Click here (this link is valid for 5 minutes):'."\r\n".Router::getBaseUrl()."auth/reset/$token");
    }
    Router::redirect("auth/sent");
  }
} else {
    $data = array('username' => '');
}
