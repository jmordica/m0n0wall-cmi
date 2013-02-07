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
 if (isset($_GET["uid"])) $uid = $_GET["uid"];
 if (isset($_POST["uid"])) $uid = $_POST["uid"];

 if (isset($mid) && isset($uid)) {
   $mono = new Monowall($mid);
   $mono->fetchFromId();
   $u = new User($uid);
   $u->fetchFromId();
   if ($u->idhost != $mono->id) {
     unset($u);
   }
 }
?>
<p class="pgtitle">User: Remove</p>
<?php
 if (isset($u)) {
   $u->delete();
   $mono->updateChanged();
   echo "User removed from database...<br/>";
 } else {
   echo "Error finding user...<br/>";
 }
?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
