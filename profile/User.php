<?php
# ג פאיכו .htaccess
#RewriteEngine on 
#RewriteRule .* - [E=MY_AUTH:%{HTTP:Authorization},L]

#if(!isset($_SERVER['PHP_AUTH_USER'])  && isset($_SERVER['MY_AUTH'])  
#&& preg_match('/Basic\s+(.*)$/i', $_SERVER['MY_AUTH'], $matches) ) { 
#     list($name, $password) = explode(':', base64_decode($matches[1])); 
#     $_SERVER['PHP_AUTH_USER'] = strip_tags($name); 
#     $_SERVER['PHP_AUTH_PW']    = strip_tags($password); 
#}  

class profile_User {
  
  private function getMysqli() {
   return new mysqli('localhost', 'bhv', 'd57LU293gxz', 'bhvdb');  
  }
  
  public $name;
  public $password;
  public $new_password;
  public $input_password = NULL;
  public $info = array();
  public $exist = false;
  
  private function getUser($name, $input_password = '') {
     $mysqli = $this->getMysqli();
     $result = $mysqli->query("select *, password('$input_password') as input_password from users where name='$name'");
     if ($result && $row = $result->fetch_array() ) {
       $this->exist = true;
       $this->name = $row['name'];
       $this->password = $row['password'];
       $this->input_password = $row['input_password'];
       $info = $row['info'];
       if (gettype($info) == "string") {
          $this->info = unserialize($info);
       }
     }else {
       $result = $mysqli->query("select password('{$mysqli->real_escape_string($input_password)}') as input_password");
       if ($result && $row = $result->fetch_array() ) {
         $this->input_password = $row['input_password'];
       }
       $this->exist = false;
       $this->name = $name;
       $this->info['logged'] = false;
       $this->info['unlogged'] = false;
       $this->info['lastLoginTime'] = time();
       $this->info['createLoginTime'] = time();
     }
     $mysqli->close();
  }

  public  function saveUser() {
     $mysqli = $this->getMysqli();
     $info = $mysqli->real_escape_string(serialize($this->info));
     if ($this->exist) {
       $mysqli->query("update users set info='$info' where name='{$mysqli->real_escape_string($this->name)}'");
     }else {
       $mysqli->query("insert into users (name, password, info)"
         . " values ('{$mysqli->real_escape_string($this->name)}', '{$mysqli->real_escape_string($this->input_password)}', '$info')");
     }
     $mysqli->close();
  }

  function __construct($name, $input_password = "") {
    $this->getUser($name, $input_password);
  }

  function validUser() {
    return $this->exist && ($this->password == $this->input_password);
  }
  
  function loggedUser() {
    return $this->validUser() && isset($this->info['logged']) && $this->info['logged'];
  }


  
  function changePassword($new_password) {
     $mysqli = $this->getMysqli();
     $new_password = $mysqli->real_escape_string($new_password);
     $mysqli->query("update users set password=password('{$new_password}') where name='{$this->name}'");
  } 
  
  public static function validateUser(){
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

      $user = new profile_User($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

      if ($user->loggedUser() && isset($user->info['lastLoginTime']) && time() - $user->info['lastLoginTime'] <= 60  ) {
        $user->info['logged'] = TRUE;  
        $user->info['lastLoginTime'] = time();
        $user->saveUser();
        return TRUE;
      }elseif ($user->validUser()){
        header('WWW-Authenticate: Basic realm="Login"');
        header('HTTP/1.0 401 Unauthorized');
        $user->info['logged'] = TRUE;  
        $user->info['lastLoginTime'] = time();
        $user->saveUser();
        exit();
      }else {
        header('WWW-Authenticate: Basic realm="Login"');
        header('HTTP/1.0 401 Unauthorized');
        exit();
      }

    }else {
      header('WWW-Authenticate: Basic realm="Login"');
      header('HTTP/1.0 401 Unauthorized');
      exit();
    }
  }
}
?>

