<?php
if ($_SERVER['REQUEST_METHOD']=='POST') {
  $data = $_POST;
  $error = "Username/password combination not valid";
  $errors['username'] = "Username/password combination not valid";
  $errors['password'] = "Username/password combination not valid";
  if (Auth::login($data['username'],$data['password'])) {
    Router::redirect("subscriptions");
  }
}