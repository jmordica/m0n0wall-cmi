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

 if (isset($_GET["mid"])) $mid = $_GET["mid"];
 if (isset($_POST["mid"])) $mid = $_POST["mid"];
 if (isset($_GET["aid"])) $aid = $_GET["aid"];
 if (isset($_POST["aid"])) $aid = $_POST["aid"];

 if (isset($mid) && isset($aid)) {
   $mono = new Monowall($mid);
   $mono->fetchFromId();
   $a = new Alias($aid);
   $a->fetchFromId();
   if ($a->idhost != $mono->id) {
     unset($a);
   }
 }
?>
<p class="pgtitle">Alias: Remove</p>
<?php
 if (isset($a)) {
    $a->delete();
   $mono->updateChanged();
   echo "Alias removed from database...<br/>";
 } else {
   echo "Error finding alias...<br/>";
 }
?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
