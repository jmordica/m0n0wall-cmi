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
 if (isset($_GET["pid"])) $pid = $_GET["pid"];
 if (isset($_POST["pid"])) $pid = $_POST["pid"];

 if (isset($mid) && isset($pid)) {
   $mono = new Monowall($mid);
   $mono->fetchFromId();
   $pa = new ProxyARP($pid);
   $pa->fetchFromId();
   if ($pa->idhost != $mono->id) {
     unset($pa);
   }
 }
?>
<p class="pgtitle">Proxy-ARP: Remove</p>
<?php
 if (isset($pa)) {
   $pa->delete();
   $mono->updateChanged();
   echo "ProxyARP removed from database...<br/>";
 } else {
   echo "Error finding proxyarp...<br/>";
 }
?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
