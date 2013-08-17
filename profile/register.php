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
  echo ('<error/><message id="password1">������: ������� ��������� ������</message>');
  echo ('<error/><message id="password2">������: ������� ��������� ������</message>');
}

if (empty($_REQUEST['password1'])){
  $isError = TRUE;
  echo ('<error/><message id="password1">������: ������ �� ����� ���� ������</message>');
}

if (empty($_REQUEST['password2'])){
  $isError = TRUE;
  echo ('<error/><message id="password2">������: ������ �� ����� ���� ������</message>');
}


if (empty($_REQUEST['email'])){
  $isError = TRUE;
  echo ('<error/><message id="email">������: email �� ����� ���� ������</message>');
}




$user = new profile_User($_REQUEST['user'], $_REQUEST['password1'] );
if ($user->exist) {
  $isError = TRUE;
  echo ("<error/><message id='user'>������: ��� ������������ {$_REQUEST['user']} ��� ������������</message>");
}

list($mailuser,$domain)=split('@', $_REQUEST['email']);
if (empty($mailuser) || empty($domain)) {
  $isError = TRUE;
  echo ('<error/><message id="email">������: ��� ��� ����� �� ����� ���� ������</message>');
}

if (! $isError) {
  if (! gethostbynamel($domain)) {
    $isError = TRUE;
    echo ("<error/><message id='email'>������: ����� $domain ����������</message>");
  }
}



if (! $isError) {
  $user->info['email'] = $_REQUEST['email'];
  $user->saveUser();
  echo('<register/>');
  echo('<alert>������������ '. htmlspecialchars($user->name).' ���������������</alert>');
}

echo ('</response>');


?>
