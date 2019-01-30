<?php
$parts = explode('.', $token);
$claims = isset($parts[1]) ? json_decode(base64_decode($parts[1]), true) : false;
$username = isset($claims['user']) ? $claims['user'] : false;

if ($_SERVER['REQUEST_METHOD']=='POST') {
  $data = $_POST;
  if (!$data['password']) {
    $errors['password'] = "Password cannot be empty";
  } elseif ($data['password']!=$data['password2']) {
    $errors['password'] = "Passwords must match"; 
    $errors['password2'] = "Passwords must match"; 
  } elseif (!NoPassAuth::login($token)) {
    $error = "Token is not valid";
  } elseif ($username) {
    Auth::update($username, $data['password']);
    Router::redirect("subscriptions");
  }
}