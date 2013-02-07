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

 $main = Main::getInstance();

 if (isset($_GET["mid"])) $mid = $_GET["mid"];
 if (isset($_POST["mid"])) $mid = $_POST["mid"];
 if (isset($mid)) { 

   $mono = new Monowall($mid);
   $mono->fetchFromId();
   $mono->fetchBuser();
 }    

function getcurlerror($ret) {
global $error, $mono;
$ok = 0;
switch($ret) {
         case 0:
          $error = $mono->error;
          break;
         case 401:
          $error = "401 Unauthorized";
          break;
         case 403:
          $error = "403 Forbidden";
          break;
         case 402:
         case 400:
         case 404:
         case 405:
         case 406:
         case 407:
         case 408:
         case 409:
         case 410:
         case 411:
         case 412:
         case 413:
         case 414:
         case 415:
         case 416:
         case 417:
         case 422:
         case 423:
         case 424:
          $error = "4XX Client Error";
         break;
         case 500:
         case 501:
         case 502:
         case 503:
         case 504:
         case 505:
         case 507:
          $error = "5XX Server error";
         break;
        default:
          echo "ok<br/>";
          echo "HTTP code returned: ".$ret."<br/>";
          $ok = 1;
          break;
       }
        return $ok;
}

?>

<p class="pgtitle">Hardware: Update</p>

<?php
$mysql = Mysql::getInstance();
$ret = $mono->hwDetect();
$ok = getcurlerror($ret);
if ($ok) {
$mysql->delete("hw-int", "WHERE `idhost`='".$mono->id."'"); /* delete current hardware interfaces */
foreach ($mono->hwInt as $eth) {
  echo $eth->name."/".$eth->mac."<br/>\n";
  if (!$eth->existsDb()) {
    $eth->insert();
    echo   "    iface inserted in db<br/>\n";
  }
}
} else {
  echo "Unable to fetch Hardware information: ".$error."<br/>";
}
?>
<br/><a href="hlist.php">Return to m0n0wall list</a><br/>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
