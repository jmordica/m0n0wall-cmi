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
 if (isset($_GET["rid"])) $rid = $_GET["rid"];
 if (isset($_POST["rid"])) $rid = $_POST["rid"];

 if (isset($mid) && isset($rid)) {
   $mono = new Monowall($mid);
   $mono->fetchFromId();
   $sr = new StaticRoute($rid);
   $sr->fetchFromId();
   if ($sr->idhost != $mono->id) {
     unset($sr);
   }
 }
?>
<p class="pgtitle">Static Route: Remove</p>
<?php
 if (isset($sr)) {
   $sr->delete();
   $mono->updateChanged();
   echo "StaticRoute removed from database...<br/>";
 } else {
   echo "Error finding static route...<br/>";
 }
?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
