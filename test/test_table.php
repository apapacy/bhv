<?php header('Content-type: text/html; charset="windows-1251"');?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
                      "http://www.w3.org/TR/html4/loose.dtd"> 

<html>
<head>
<title>Документ</title>
<link rel=stylesheet type="text/css" href="../combobox/combobox.css">
<link rel=stylesheet type="text/css" href="../table/table.css">
</head>
<body >
<script type="text/javascript" src="../bhv/util.js"></script>
<script type="text/javascript" src="../table/Table.js"></script>
<script type="text/javascript" src="../combobox/Combobox.js"></script>
<span id="combobox1" style="width:200px" ></span>
<span id="combobox2" style="width:200px" ></span>
<div id="tab1" ></div> 
<script type="text/javascript">
table1 = new bhv.Table("tab1", "test/table_doc_det.xml");
</script>
<a href="print_doc.php" target="print_doc">Весь документ (в соседней вкладке). Для обновления нужно кликнуть ссылку</a>
</body>
</html>