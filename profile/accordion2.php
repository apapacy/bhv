<?php
header("Content-Type: text/html; charset=\"windows-1251\"");
require('../profile/User.php');
profile_User::validateUser();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>Ajax: accordion</title>
<link rel="stylesheet" type="text/css" href="../accordion/accordion.css">

<script type="text/javascript" src="../accordion/accordion.js"></script>

</head>
<body onload='accordionAddBehivior("accordion1");'>

<h1>��������� "���������"</h1>

<div id="accordion1">

<div>��������� ������� �������</div>

<div>
������ ������ ���������� "���������"
����� ��������� ��� �������������� �������� ��������.
</div>

<div>��������� ������� ������� (click me)</div>

<div>
������ � ����������� ������� ���������� "���������"
����� ��� �������������� �������� ��������.
</div>

<div>��������� �������� �������</div>

<div>
��� ����, ����� ��������� ��������� ������ ���������� "���������"
���������� �������� ����� ������� ���� �� ��������� �������.
</div>

<div>��������� ���������� �������</div>

<div>
������ ����� ���� �������� � ���������� ������� ������. � ����� ...
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
... � �� ���������� � ������� ������ ��� ����������. ��� ���� ������.
</div>

</div>


</body>
</html>

