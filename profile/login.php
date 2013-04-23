<?php
header("Content-Type: text/plain; charset=\"windows-1251\"");
require('User.php');
profile_User::validateUser();

/*if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

  $user = new profile_User($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

    if ($user->loggedUser() ) {
      $user->info['logged'] = TRUE;  
      $user->info['lastLoginTime'] = time();
      $user->saveUser();
    }elseif ($user->validUser() ) {
      if (!isset($user->info['lastLoginTime']) || time() - $user->info['lastLoginTime'] > 10 ) {
        header('WWW-Authenticate: Basic realm="Login ' . rand(11111111,99999999) . '"');
        header('HTTP/1.0 401 Unauthorized');
      }
      $user->info['logged'] = TRUE;  
      $user->info['lastLoginTime'] = time();
      $user->saveUser();
    }else {
      header('WWW-Authenticate: Basic realm="Login ' . rand(11111111,99999999) . '"');
      header('HTTP/1.0 401 Unauthorized');
    }

}else {
  header('WWW-Authenticate: Basic realm="Login ' . rand(11111111,99999999) . '"');
  header('HTTP/1.0 401 Unauthorized');
}*/

?> 
