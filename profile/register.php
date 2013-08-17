<?php

header('Content-Type: application/xml; charset="Windows-1251"');

echo "<?xml version=\"1.0\" encoding=\"Windows-1251\"?>\n\n";

$isError = FALSE;

require('User.php');

$_REQUEST['user'] = trim($_REQUEST['user']);
$_REQUEST['password1'] = trim($_REQUEST['password1']);
$_REQUEST['password2'] = trim($_REQUEST['password2']);
$_REQUEST['email'] = trim($_REQUEST['email']);


echo ('<response>');
if ($_REQUEST['password1'] != $_REQUEST['password2']){
  $isError = TRUE;
  echo ('<error/><message id="password1">Сервер: Введены различные пароли</message>');
  echo ('<error/><message id="password2">Сервер: Введены различные пароли</message>');
}

if (empty($_REQUEST['password1'])){
  $isError = TRUE;
  echo ('<error/><message id="password1">Сервер: пароль не может быть пустым</message>');
}

if (empty($_REQUEST['password2'])){
  $isError = TRUE;
  echo ('<error/><message id="password2">Сервер: пароль не может быть пустым</message>');
}


if (empty($_REQUEST['email'])){
  $isError = TRUE;
  echo ('<error/><message id="email">Сервер: email не может быть пустым</message>');
}




$user = new profile_User($_REQUEST['user'], $_REQUEST['password1'] );
if ($user->exist) {
  $isError = TRUE;
  echo ("<error/><message id='user'>Сервер: имя пользователя {$_REQUEST['user']} уже используется</message>");
}

list($mailuser,$domain)=split('@', $_REQUEST['email']);
if (empty($mailuser) || empty($domain)) {
  $isError = TRUE;
  echo ('<error/><message id="email">Сервер: имя или домен не может быть пустым</message>');
}

if (! $isError) {
  if (! gethostbynamel($domain)) {
    $isError = TRUE;
    echo ("<error/><message id='email'>Сервер: домен $domain недоступен</message>");
  }
}



if (! $isError) {
  $user->info['email'] = $_REQUEST['email'];
  $user->saveUser();
  echo('<register/>');
  echo('<alert>Пользователь '. htmlspecialchars($user->name).' зарегистрирован</alert>');
}

echo ('</response>');


?>
