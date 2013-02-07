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

 if (isset($_GET["gaid"])) $gaid = $_GET["gaid"];
 if (isset($_POST["gaid"])) $gaid = $_POST["gaid"];

 if (isset($gaid)) {
   $a = new GAlias($gaid);
   $a->fetchFromId();
 }
?>
<p class="pgtitle">Global Alias: Remove</p>
<?php
 if (isset($a)) {
   $a->delete();
   echo "Global Alias removed from database...<br/>";
 } else {
   echo "Error finding alias...<br/>";
 }
?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
