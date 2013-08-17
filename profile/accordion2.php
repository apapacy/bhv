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

<h1>Компонент "Аккордеон"</h1>

<div id="accordion1">

<div>Заголовок первого раздела</div>

<div>
Первый раздел компонента "Аккордеон"
будет отображен при первоначальной загрузке страницы.
</div>

<div>Заголовок второго раздела (click me)</div>

<div>
Второй и последующие разделы компонента "Аккордеон"
срыты при первоначальной загрузке страницы.
</div>

<div>Заголовок третьего раздела</div>

<div>
Для того, чтобы раскрылся очередной раздел компонента "Аккордеон"
необходимо кликнуть левой кнопкой мыши по заголовку раздела.
</div>

<div>Заголовок четвертого раздела</div>

<div>
Раздел может быть коротким и помещаться текущей панели. А может ...
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
... и не помещаться в текущей панели без скроллинга. Как этот раздел.
</div>

</div>


</body>
</html>

