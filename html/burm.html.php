<?php
 /**
  * Base file for HTML processing
  *
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package html
  * @subpackage html
  * @category html
  * @filesource
  */
?>
<?php
 require_once("./lib/autoload.lib.php");
 require_once("./lib/html.lib.php");

 /* sanitize _GET and _POST */
 sanitizeArray($_GET);
 sanitizeArray($_POST);

 /* connect to the database: */
 if (!Mysql::getInstance()->connect())
 { echo "Cannot connect to mysql database<br/>\n"; die(); }

 if (isset($_GET["bid"])) {
  $bu = new Buser(mysql_escape_string($_GET["bid"]));
 }
 else {
  echo "No correct arguments.<br/>";
 }

 $main = Main::getInstance();
 $main->fetchMonoId();
 $main->fetchMonoDetails();
?>

<p class="pgtitle">Backup user: Remove</p>

<?php if ($bu->fetchFromId()) {
   
   foreach($main->monowall as $mono) {
     if ($mono->idbuser == $bu->id) {
       $mono->idbuser = -1;
       $mono->buser = NULL;
       $mono->update();
     }
   }
   $bu->delete();
    echo "Backup user removed. <br/>";
  } else {
    echo "Backup user specified does not exists. <br/>";
  }
?>

<br/><a href="busers.php">Return to backup users list</a>

<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
<br/><a href="busers.php">Return to Backup Users list</a><br/>
