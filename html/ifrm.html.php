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
 if (isset($_GET["iid"])) $iid = $_GET["iid"];
 if (isset($_POST["iid"])) $iid = $_POST["iid"];
 
 if (isset($mid) && $mid) {
   $mono = new Monowall($mid);
   $mono->fetchFromId();
   $mono->fetchIfaces();
   $mono->fetchIfacesDetails();

   if (isset($iid) && $iid) {
   $if = new Iface($iid);
   $if->fetchFromId();
   } 
 } else die("No group or monowall found.");


?>
<p class="pgtitle">Interface: Removal</p>

<?php
 if ($if->type == "lan" || $if->type == "wan") die("Unable to delete LAN/WAN interface<br/>");
 $num = $if->num;
 $if->delete();
 foreach($mono->ifaces as $iface) {
   if ($iface->num > $num) {
     $iface->num--;
     $iface->update();
   }
 }
?>
Interface Correctly deleted.<br/>

<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
