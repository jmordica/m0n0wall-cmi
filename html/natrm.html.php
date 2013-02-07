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
 if (isset($_GET["type"])) $type = $_GET["type"];
 if (isset($_POST["type"])) $type = $_POST["type"];
 if (isset($_GET["nid"])) $nid = $_GET["nid"];
 if (isset($_POST["nid"])) $nid = $_POST["nid"];
 if (!isset($type)) $type = 1;

 if (isset($mid) && $mid && isset($type) && isset($nid) && $nid) {
  $mono = new Monowall($mid);
  $mono->fetchFromId();
  switch($type) {
    case 1:
	$nat = new RuleNat($nid);
    break;
    case 2:
	$nat = new SrvNat($nid);
    break;
    case 3:
	$nat = new O2ONat($nid);
    break;
    case 4:
	$nat = new AdvNat($nid);
    break;
  }
  $nat->fetchFromId();
 } 

?>
<p class="pgtitle">NAT: Remove</p>
<?php
 if (isset($nat)) {
   $nat->delete();
   $mono->updateChanged();
   echo "NAT entry removed from database...<br/>";
 } else {
   echo "Error finding NAT entry...<br/>";
 }
?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
