<?php
header('Content-Type: application/xml; charset="Windows-1251"');
echo "<?xml version=\"1.0\" encoding=\"Windows-1251\"?>\n\n";


require('User.php');
$user = new profile_User($_REQUEST['user']);
if ($user->exist) {
  echo ("<response><error/><message>Пользователь " . htmlspecialchars($_REQUEST['user']). " уже существует</message></response>");
} else {
  echo ("<response><message>Пользователь " . htmlspecialchars($_REQUEST['user']). " разрешена регистрация</message></response>");
}

?>
