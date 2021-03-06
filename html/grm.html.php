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
 if (isset($_GET["gid"])) $gid = $_GET["gid"];
 if (isset($_POST["gid"])) $gid = $_POST["gid"];

 if (isset($mid) && isset($gid)) {
   $mono = new Monowall($mid);
   $mono->fetchFromId();
   $g = new Group($gid);
   $g->fetchFromId();
   if ($g->idhost != $mono->id) {
     unset($g);
   }
 }
?>
<p class="pgtitle">Group: Remove</p>
<?php
 if (isset($g)) {
   $g->delete();
   $mono->updateChanged();
   echo "Group removed from database...<br/>";
 } else {
   echo "Error finding group...<br/>";
 }
?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
