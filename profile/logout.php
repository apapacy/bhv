<?php 
header("Content-Type: text/plain; charset=\"windows-1251\"");
require('User.php');
if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) ) {
  $user = new profile_User($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

  if ($user->validUser()) {
    if (isset($user->info["logged"]) && ! $user->info["logged"]){ 
      exit();
    }
    $user->info["logged"] = FALSE;  
    $user->saveUser();
  }
}
header('WWW-Authenticate: Basic realm="Must enter valid password message# ' . rand(11111111,99999999) . '"');
header('HTTP/1.0 401 Unauthorized');

?>
