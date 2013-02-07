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

 if (isset($_GET["mid"])) $mid = mysql_escape_string($_GET["mid"]);
 if (isset($_POST["mid"])) $mid = mysql_escape_string($_POST["mid"]);
 
 $main = Main::getInstance();

 if (isset($mid) && $mid) {
  $mono = new Monowall($mid);
  $mono->fetchFromId();
  $mono->fetchBuser();
  $mono->fetchProp();
  $mono->fetchIfaces();
  $main->fetchRulesId();
  $main->fetchRulesDetails();
  $mono->fetchRules();
  $mono->fetchSyslog();
  $mono->fetchSnmp();
  $mono->fetchHw();
 }
?>
<p class="pgtitle">m0n0wall: Delete</p>
<?php 

if (isset($mono)) {
   $mysql = Mysql::getInstance();
 
              /* if rule isn't used anymore, delete it from db */
   foreach($mono->ifaces as $if) {
     foreach($if->rulesint as $ri) {
       $ri->delete();
       $index = "`idrule`";
       $table = "rules-int";
       $where = "WHERE `idrule`='".$ri->idrule."'";
       $m = Mysql::getInstance();
       $data = $m->select("idrule", $table, $where);
       if (!count($data)) {
         $ru = new Rule($ri->idrule);
         $ru->fetchFromId();
         $ru->delete();
         echo "Rule number ".$ru->id." deleted totally from database as there were no longer monowall using it...<br/>";
       }
     }
     //$mysql->delete("rules-int", "WHERE `idint`='".$if->id."'"); 	/* delete rules-int*/
   }
   $mysql->delete("alias", "WHERE `idhost`='".$mono->id."'"); 		/* delete aliases */
   $mysql->delete("group", "WHERE `idhost`='".$mono->id."'"); 		/* delete groups */
   $mysql->delete("user", "WHERE `idhost`='".$mono->id."'"); 		/* delete users */
   $mysql->delete("interfaces", "WHERE `idhost`='".$mono->id."'"); 	/* delete interfaces */
   $mysql->delete("nat-advout", "WHERE `idhost`='".$mono->id."'"); 	/* delete NAT */
   $mysql->delete("nat-one2one", "WHERE `idhost`='".$mono->id."'"); 	/* delete NAT */
   $mysql->delete("nat-rules", "WHERE `idhost`='".$mono->id."'"); 	/* delete NAT */
   $mysql->delete("nat-srv", "WHERE `idhost`='".$mono->id."'"); 	/* delete NAT */
   $mysql->delete("properties", "WHERE `idhost`='".$mono->id."'"); 	/* delete properties */
   $mysql->delete("proxyarp", "WHERE `idhost`='".$mono->id."'"); 	/* delete proxyarp */
   $mysql->delete("snmp", "WHERE `id`='".$mono->idsnmp."'"); 		/* delete snmp */
   $mysql->delete("syslog", "WHERE `id`='".$mono->idsyslog."'"); 	/* delete syslog */
   $mysql->delete("staticroutes", "WHERE `idhost`='".$mono->id."'"); 	/* delete static routes */
   $mysql->delete("vlans", "WHERE `idhost`='".$mono->id."'"); 		/* delete vlans */
   $mysql->delete("hw-int", "WHERE `idhost`='".$mono->id."'"); 		/* HW interface */
   $mysql->delete("unknown", "WHERE `idhost`='".$mono->id."'"); 	/* Unknown objects */

   $mono->delete();
   ?>m0n0wall deleted from database...<br/><?php
 } else {
  echo "No monowall selected for removal<br/>";
 }

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
<br/><a href="hlist.php">Return to m0n0wall list</a><br/>
