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
 require_once("./lib/html.lib.php");
 require_once("./lib/autoload.lib.php");

 /* sanitize _GET and _POST */
 sanitizeArray($_GET);
 sanitizeArray($_POST);

 /* connect to the database: */
 if (!Mysql::getInstance()->connect())
 { echo "Cannot connect to mysql database<br/>\n"; die(); }

 if (isset($_GET["mid"])) $mid = $_GET["mid"];
 if (isset($_POST["mid"])) $mid = $_POST["mid"];
 if (isset($_GET["vid"])) $vid = $_GET["vid"];
 if (isset($_POST["vid"])) $vid = $_POST["vid"];

 if (isset($mid) && isset($vid)) {
   $mono = new Monowall($mid);
   $mono->fetchFromId();
   $v = new Vlan($vid);
   $v->fetchFromId();
   if ($v->idhost != $mono->id) {
     unset($v);
   }
 }
?>
<p class="pgtitle">VLAN: Remove</p>
<?php
 if (isset($v)) {
   $v->delete();
   $mono->updateChanged();
   echo "Vlan removed from database...<br/>";
 } else {
   echo "Error finding vlan...<br/>";
 }
?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
