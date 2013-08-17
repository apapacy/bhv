<?php
class ButtonModel {

  private $pressed = 0;

  function ButtonModel($state){
    if ($state == "pressed") {
      $this->pressed = 1;
    }
  }
  
  function togglePressed() {
    if ($this->pressed == 0) {
      $this->pressed = 1;
    } else {
      $this->pressed = 0;
    } 
  }

  function isPressed() {
    return  $this->pressed == 1;
  }
}


class ButtonView {
   static function renderModel($model){
    echo '<span id="button" class=';
      if ($model->isPressed()) {
        echo "'buttonPressed'";
      } else {
        echo "'button'";
      }
    echo "onclick=\"javascript:document.location.href='button.php'\">Кнопка</span>";
  }

}

///////////////////////////////
// Button Controller
///////////////////////////////
session_start();
if (! isset($_SESSION['button'])) {
  $_SESSION['button'] = new ButtonModel("");
} else {
  $_SESSION['button']->togglePressed();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
                      "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>Ajax: accordion</title>
<link rel="stylesheet" type="text/css" href="button.css">
</head>
<body>
<div style="padding:8px">
<?php
ButtonView::renderModel($_SESSION['button']);
?>
</div>
</body>
<html>