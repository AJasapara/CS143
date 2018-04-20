<!DOCTYPE html>
<html>
  <head>
    <title>Calculator</title>
  </head>

  <body>
    <h1>Calculator</h1>

    <form method="GET">
      <input type="text" name="query"/>
      <input type="submit" value="calculate"/>
    </form>


    <?php
    if (preg_match("/[a-z]/i",$_GET["query"])) {
      echo "Invalid expression!";
      // doesn't check for cases like 1----3
    }
    else {
      eval('echo '.$_GET['query'].';');
      //if(isset($_GET['query'])){eval('echo '.$_GET['query'].';');}
      // else {
      //   echo "Invalid";
      // }
    }
    ?>
  </body>

</html>

