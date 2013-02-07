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
  *
  * @todo split curl error handling into a obj/lib
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


 if (isset($_GET["action"])) $action = $_GET["action"];
 if (isset($_POST["action"])) $action = $_POST["action"];

 $main = Main::getInstance();
 if (isset($action)) {
  $main->fetchMonoId();
  $main->fetchMonoDetails();
 } 
 $main->fetchBusers();

function getcurlerror($ret) {
global $error, $mono;
$ok = 0;
switch($ret) {
         case 0:
          $error = $mono->config->error;
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

<p class="pgtitle">m0n0wall: Import</p>

<?php
 if (!isset($action) || $action == 0) { $action = 1; ?>

<form action="import.php" method="post">
<input type="hidden" value="<?=$action; ?>" name="action">
<table width="100%" border="0" cellpadding="6" cellspacing="0">
<tr> 
 <td width="22%" valign="top" class="vncellreq">Hostname</td>
 <td width="78%" class="vtable"><input name="hostname" type="text" class="formfld" id="hostname" size="40" value=""> 
 <br> <span class="vexpl">name of the firewall host, without 
  domain part<br>
  e.g. <em>firewall</em></span></td>
</tr>
<tr> 
 <td width="22%" valign="top" class="vncellreq">Domain</td>
 <td width="78%" class="vtable"><input name="domain" type="text" class="formfld" id="domain" size="40" value=""> 
  <br> <span class="vexpl">e.g. <em>mycorp.com</em> </span></td>
</tr>
<tr> 
 <td width="22%" valign="top" class="vncellreq">IP Address</td>
 <td width="78%" class="vtable"><input name="ip" type="text" class="formfld" id="ip" size="40" value=""> 
  <br> <span class="vexpl">IP Address to use if the DNS is not resolvable</span>
  <br/>
  <input name="use_ip" type="checkbox" id="use_ip" value="1" >
  <strong>Use ip address to backup/restore configuration of monowall device</strong>
 </td>
</tr>
<tr> 
 <td width="22%" valign="top" class="vncellreq">Port</td>
 <td width="78%" class="vtable"><input name="port" type="text" class="formfld" id="port" size="10" value="443"> 
  <br> <span class="vexpl">Port to use to connect to monowwall device</span>
  <br/>
  <input name="https" type="checkbox" id="https" value="1" checked>
  <strong>Use HTTPS</strong>
 </td>
</tr>

  <tr>
   <td width="22%" valign="top" class="vncellreq">Backup user</td>
   <td width="78%" class="vtable"><select name="buser" id="buser"><?php
     foreach ($main->busers as $buser) {
       ?><option value="<?php echo $buser->id; ?>"><?php echo $buser->login; ?></option><?php
     }
    ?>
     </select>
     <br/> <span class="vexpl">Select the backup user to use together with this monowall<br/></span></td>
  </tr>
 <tr> 
  <td width="22%" valign="top">&nbsp;</td>
  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="Import"> 
  </td>
 </tr>
</table>
</form>
<?php
 }
  else {
   if ($action == 1) {
     
     if (!isset($_POST["buser"]) || empty($_POST["buser"])) { die("No backup user selected"); } else {
       
       $bu = new Buser(mysql_escape_string($_POST["buser"]));
       if (!$bu->fetchFromId()) {
         die("Backup user can't be fetched...");
       }
     }

     if (isset($_POST["hostname"]) && isset($_POST["domain"])) {
       
       /* hostname can't already be in database */
       foreach($main->monowall as $mono) {
         if ($mono->hostname == mysql_escape_string($_POST["hostname"]) && $mono->domain == mysql_escape_string($_POST["domain"])) {
           echo "Error, monowall already in database...<br/>";
           die();
         }
       }
       
       /* if use_ip is set, ip should be too */
       if (isset($_POST["use_ip"]) && $_POST["use_ip"] == 1) {
        if (!isset($_POST["ip"]) || empty($_POST["ip"])) {
	  echo "Error with IP address<br/>"; die();
        }
       }
       $mono = new Monowall();
       $mono->idbuser = $bu->id;
       $mono->buser = $bu;
       $mono->hostname = mysql_escape_string($_POST["hostname"]);
       $mono->domain = mysql_escape_string($_POST["domain"]);
       if (isset($_POST["use_ip"]) && $_POST["use_ip"] == 1) {
         $mono->use_ip = 1;
       }
       $mono->ip = mysql_escape_string($_POST["ip"]);
       if (checkPost("https") && $_POST["https"] == 1) $mono->https = 1; else $mono->https = 0;
       if (checkPost("port")) $mono->port = $_POST["port"];
       echo "Fetching & parsing configuration...";
       $ret = $mono->config->fetchConfig();
       $ok = getcurlerror($ret);
       if (!$ok) {
	 echo "failed<br/>";
         echo "Error returned: ".$error."<br/>";
         echo "Removing m0n0wall base object from DB...";
         $mono->delete();
         echo "done<br/>";
       }
       else if ($ok) {
        $mono->config->parseConfig();
        $mono->config->fillObj();
        echo "ok<br/>";

        echo "Inserting m0n0wall base object...";
        /* add monowall to db */
        $mono->insert();
        $mono->id = Mysql::getInstance()->getLastId();
        echo "ok<br/>";

        /* insert everything */

        echo "Inserting properties...";
        foreach ($mono->prop as $p) {
	  $p->idhost = $mono->id;
          if ($p->existsInDb()) {
            $p->fetchId();
            if ($p->ischanged()) {
 	     $p->update();
       	   }
          } else {
 	   $p->insert();
 	   $p->id = Mysql::getInstance()->getLastId();
          }
        }
        echo "ok<br/>";

        echo "Inserting syslog settings...";
        if ($mono->syslog->existsInDb()) {
          $mono->syslog->fetchId();
          $mono->idsyslog = $mono->syslog->id;
 
          if ($mono->syslog->ischanged()) {
            $mono->syslog->update();
          }
        }
        else { 
         $mono->syslog->insert(); 
         $mono->syslog->fetchId();
         $mono->idsyslog = $mono->syslog->id;
        }
        echo "ok<br/>";
 
        echo "Inserting SNMP settings...";
        if ($mono->snmp->existsInDb())
        {
          $mono->snmp->fetchId();
         $mono->idsnmp = $mono->snmp->id;
  
          if ($mono->snmp->ischanged()) {
           $mono->snmp->update();
          }
        }
        else { 
         $mono->snmp->insert(); 
         $mono->snmp->fetchId();
         $mono->idsnmp = $mono->snmp->id;
        }
        echo "ok<br/>";
 
        echo "Inserting Groups...";
        foreach ($mono->group as $group) {
 	 $group->idhost = $mono->id; 
         if ($group->existsInDb())
         {
           $group->fetchId();
 
           if ($group->ischanged()) {
             $group->update();
           }
         } else {
          $group->insert();
          $group->fetchId();
         }
        }
        echo "ok<br/>";
 
        echo "Inserting Users...";
        foreach ($mono->user as $user) {
 	 $user->idhost = $mono->id; 
         if ($user->existsInDb())
         {
           $user->fetchId();
 
           if ($user->ischanged()) {
             $user->update();
           }
         }
         else {
          $user->insert();
          $user->fetchId();
         }
       }
       echo "ok<br/>";
 
       echo "Updating m0n0wall base object...";
       if ($mono->ischanged() == 1) {
        $mono->update();
       }
       echo "ok<br/>";
 
       echo "Inserting Interfaces ...";
       foreach ($mono->ifaces as $iface) {
 	 $iface->idhost = $mono->id; 
         if ($iface->existsInDb())
         {
           if ($iface->ischanged())
           {
             $iface->update();
           }
         }
         else {
          $iface->insert();
         }
       }
       echo "ok<br/>";
 
       echo "Inserting VLANs...";
       foreach ($mono->vlans as $vlan) {
 	 $vlan->idhost = $mono->id; 
         if ($vlan->existsInDb())
         { 
           if ($vlan->ischanged())
           { 
             $vlan->update();
           }
         }
         else {
          $vlan->insert();
         }
       }
       echo "ok<br/>";
 
       echo "Inserting Local Aliases...";
       foreach ($mono->alias as $alias) {
 	 $alias->idhost = $mono->id; 
         if ($alias->existsInDb())
         { 
           if ($alias->ischanged())
           { 
             $alias->update();
           }
         }
         else {
          $alias->insert();
         }
       }
       echo "ok<br/>";
       
       echo "Inserting ProxyARP...";
       foreach ($mono->proxyarp as $pa) {
 
 	 $pa->idhost = $mono->id; 
         if ($pa->existsInDb())
         { 
            if ($pa->ischanged())
            { 
              $pa->update();
            }
         }
         else {
          $pa->insert();
         }
       }
       echo "ok<br/>";
       
       echo "Inserting Static Routes...";
       foreach ($mono->sroutes as $sroute) {
 
 	 $sroute->idhost = $mono->id; 
         if ($sroute->existsInDb())
         { 
           if ($sroute->ischanged())
           { 
             $sroute->update();
           }
         }
         else {
          $sroute->insert();
         }
       }
       echo "ok<br/>";
 
       foreach($mono->ifaces as $iface) {
    
 	 $iface->idhost = $mono->id; 
         echo "Inserting rules for iface ".$iface->type;
 	echo ($iface->num != 0)?$iface->num:"";
 	echo "...";
         foreach($iface->rules as $rule) {
           if ($rule->existsInDb()) {
             $rule->fetchId();
             if($rule->ischanged()) $rule->update();
           } else { 
 	   $rule->insert();
 	   $rule->id = Mysql::getInstance()->getLastId();
           }
         }
         $main->removeRuleIntIf($iface->id);
         RuleInt::dropAllIface($iface->id);
         $iface->rulesint = array();
         reset($iface->rules);
         $i = 0;
         foreach($iface->rules as $rule) {
           $ri = new RuleInt($rule->id, $iface->id, $iface->rulesp[$i][1], $iface->rulesp[$i][0]);
           $ri->iface = $iface;
           $ri->rule = $main->getRule($ri->idrule);
           array_push($main->ruleint, $ri);
           array_push($iface->rulesint, $ri);
           $riarray[$ri->position] = $ri;
           $i++;
         }
         reset($riarray);
         foreach($riarray as $ri) {
           $ri->insert();
         }
         echo "ok<br/>";
       }
 
       echo "Inserting normal NAT rules...";
       foreach ($mono->nat as $nat)
       {
 	 $nat->idhost = $mono->id; 
         if ($nat->existsInDb())
         {
           if ($nat->ischanged())
           { 
             $nat->update();
           }
         } else {
          $nat->insert();
         }    
       }
       echo "ok<br/>";
  
       echo "Inserting Advanced NAT rules...";
       foreach ($mono->advnat as $nat)
       {
 	 $nat->idhost = $mono->id; 
         if ($nat->existsInDb())
         {
           if ($nat->ischanged())
           { 
              $nat->update();
           }
         } else {
          $nat->insert();
          }    
       }
       echo "ok<br/>";
 
       echo "Inserting Server NAT...";
       foreach ($mono->srvnat as $nat)
       {
 	 $nat->idhost = $mono->id; 
         if ($nat->existsInDb())
         {
           if ($nat->ischanged())
           { 
             $nat->update();
           }
         } else {
          $nat->insert();
         }    
       }
       echo "ok<br/>";
 
       echo "Inserting One to One NAT...";
       foreach ($mono->o2onat as $nat)
       {
 	 $nat->idhost = $mono->id; 
         if ($nat->existsInDb())
          {
           if ($nat->ischanged())
           { 
             $nat->update();
           }
         } else {
          $nat->insert();
         }    
       }
       echo "ok<br/>";
 
	echo "Inserting all remaining object...";
      foreach ($mono->unknown as $unknown)
      {
        $unknown->idhost = $mono->id;
        $unknown->insert();
        $unknown->id = Mysql::getInstance()->getLastId();
      }
      foreach ($mono->unknown as $unknown) {
        if ($unknown->parent) {
          $unknown->idparent = $unknown->parent->id;
          $unknown->update();
        }
      }
	echo "done<br/>";

       echo "<br/>Monowall inserted into database..<br/>";
      
       $mysql = Mysql::getInstance();
	echo "Get last changed config time...";
       $ret = $mono->updateLChange();
	$ok = getcurlerror($ret);
       if (!$ok) {
         echo "failed<br/>";
       } else {
 	 echo "done<br/>";
	}
       $ret = $mono->hwDetect();
	$ok = getcurlerror($ret);
       if (!$ok) {
         echo "failed<br/>";
       } else {
       $mysql->delete("hw-int", "WHERE `idhost`='".$mono->id."'"); /* delete current hardware interfaces */
 	foreach ($mono->hwInt as $eth) {
 	  echo $eth->name."/".$eth->mac."<br/>\n";
 	  if (!$eth->existsDb()) {
 	    $eth->insert();
 	    echo   "    iface inserted in db<br/>\n";
 	  }
 	}
         echo "done<br/>";
 
        }
     } /* if $ok */
     }
     else {
       echo "Error with hostname/domain name not set.<br/>";
     }
   } else { ?>
 Incorrect action.
<?php }
 }

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
