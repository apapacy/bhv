<?php
header('Content-Type: application/xml; charset="Windows-1251"');
echo "<?xml version=\"1.0\" encoding=\"Windows-1251\"?>\n\n";


require('User.php');
$user = new profile_User($_REQUEST['user']);
if ($user->exist) {
  echo ("<response><error/><message>������������ " . htmlspecialchars($_REQUEST['user']). " ��� ����������</message></response>");
} else {
  echo ("<response><message>������������ " . htmlspecialchars($_REQUEST['user']). " ��������� �����������</message></response>");
}

?>
