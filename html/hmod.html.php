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
 
 $main = Main::getInstance();

 if (isset($mid) && $mid) {
  $mono = new Monowall($mid);
  $mono->fetchFromId();
  $mono->fetchBuser();
  $mono->fetchProp();
 }
?>
<p class="pgtitle">m0n0wall: Edit</p>

<?php if (!isset($_POST["action"]) || $_GET["action"] == 2) { ?>
 <?php if ($_GET["action"] == 2) $mono = new Monowall(); ?>

<form action="hmod.php" method="post">
<input type="hidden" value="<?php if (isset($_GET["action"]) && $_GET["action"] == 2) echo 3; else echo 1; ?>" name="action">
<input type="hidden" value="<?php echo $mono->id; ?>" name="mid">
<table width="100%" border="0" cellpadding="6" cellspacing="0">
<tr> 
 <td width="22%" valign="top" class="vncellreq">Hostname</td>
 <td width="78%" class="vtable"><input name="hostname" type="text" class="formfld" id="hostname" size="40" value="<?php echo $mono->hostname; ?>"> 
 <br> <span class="vexpl">name of the firewall host, without 
  domain part<br>
  e.g. <em>firewall</em></span></td>
</tr>
<tr> 
 <td width="22%" valign="top" class="vncellreq">Domain</td>
 <td width="78%" class="vtable"><input name="domain" type="text" class="formfld" id="domain" size="40" value="<?php echo $mono->domain; ?>"> 
  <br> <span class="vexpl">e.g. <em>mycorp.com</em> </span></td>
</tr>
<tr> 
 <td width="22%" valign="top" class="vncellreq">IP Address</td>
 <td width="78%" class="vtable"><input name="ip" type="text" class="formfld" id="ip" size="40" value="<?php echo $mono->ip; ?>"> 
  <br> <span class="vexpl">IP Address to use if the DNS is not resolvable</span>
  <br/>
  <input name="use_ip" type="checkbox" id="use_ip" value="1" <?php if ($mono->use_ip) echo "checked"; ?>>
  <strong>Use ip address to backup/restore configuration of monowall device</strong>
 </td>
</tr>
<tr> 
 <td width="22%" valign="top" class="vncellreq">Port</td>
 <td width="78%" class="vtable"><input name="port" type="text" class="formfld" id="ip" size="10" value="<?php echo $mono->port; ?>"> 
  <br> <span class="vexpl">Port to access the interface</span>
  <br/>
  <input name="https" type="checkbox" id="https" value="1" <?php if ($mono->https) echo "checked"; ?>>
  <strong>Use HTTPS</strong>
 </td>
</tr>
<tr> 
 <td width="22%" valign="top" class="vncell">DNS servers</td>
 <td width="78%" class="vtable">
  <?php $dns = explode(";", $mono->dnsserver); ?>
  <input name="dns1" type="text" class="formfld" id="dns1" size="20" value="<?php echo $dns[0]; ?>">
  <br>
  <input name="dns2" type="text" class="formfld" id="dns2" size="20" value="<?php echo $dns[1]; ?>">
  <br>
  <input name="dns3" type="text" class="formfld" id="dns3" size="20" value="<?php echo $dns[2]; ?>">
  <br>
  <span class="vexpl">IP addresses; these are also used for 
  the DHCP service, DNS forwarder and for PPTP VPN clients<br>
  <br>
  <input name="dnsallowoverride" type="checkbox" id="dnsallowoverride" value="1" <?php if ($mono->dnsoverride) echo "checked"; ?> >
  <strong>Allow DNS server list to be overridden by DHCP/PPP 
  on WAN</strong><br>
  If this option is set, m0n0wall will use DNS servers assigned 
  by a DHCP/PPP server on WAN for its own purposes (including 
  the DNS forwarder). They will not be assigned to DHCP and 
  PPTP VPN clients, though.</span></td>
 </tr>
 <tr> 
  <td valign="top" class="vncell">Username</td>
  <td class="vtable"> <input name="username" type="text" class="formfld" id="username" size="20" value="<?php echo $mono->username; ?>">
  <br>
  <span class="vexpl">If you want 
  to change the username for accessing the webGUI, enter it 
  here.</span></td>
 </tr>
 <tr> 
  <td width="22%" valign="top" class="vncell">Password</td>
  <td width="78%" class="vtable"> <input name="password" type="password" class="formfld" id="password" size="20"> 
  <br> <input name="password2" type="password" class="formfld" id="password2" size="20"> 
  &nbsp;(confirmation) <br> <span class="vexpl">If you want 
  to change the password for accessing the webGUI, enter it 
  here twice.</span></td>
 </tr>
<!--
 <tr> 
  <td width="22%" valign="top" class="vncell">webGUI protocol</td>
  <td width="78%" class="vtable"> <input name="webguiproto" type="radio" value="http" >
   HTTP &nbsp;&nbsp;&nbsp; <input type="radio" name="webguiproto" value="https" checked>
   HTTPS</td>
 </tr>
 <tr> 
  <td valign="top" class="vncell">webGUI port</td>
  <td class="vtable"> <input name="webguiport" type="text" class="formfld" id="webguiport" size="5" value=""> 
  <br>
  <span class="vexpl">Enter a custom port number for the webGUI 
  above if you want to override the default (80 for HTTP, 443 
  for HTTPS).</span></td>
 </tr>
-->
 <tr> 
  <td width="22%" valign="top" class="vncell">Time zone</td>
  <td width="78%" class="vtable"> <select name="timezone" id="timezone">
<?php
   $fp = fopen ("./inc/timezone.txt", "r");
   if ($fp) {
     while($line = fgets($fp)) {
       $line = trim($line);
       ?><option value="<?php echo $line; ?>"<?php if ($mono->timezone == $line) echo " selected"; ?>><?php echo $line; ?></option>
       <?php
     }
     fclose($fp);
   }
?>
   </select> <br> <span class="vexpl">Select the location closest 
   to you</span></td>
 </tr>
 <tr> 
  <td width="22%" valign="top" class="vncell">Time update interval</td>
  <td width="78%" class="vtable"> <input name="timeupdateinterval" type="text" class="formfld" id="timeupdateinterval" size="4" value="<?php echo $mono->ntpinterval; ?>"> 
  <br> <span class="vexpl">Minutes between network time sync.; 
  300 recommended, or 0 to disable </span></td>
 </tr>
 <tr> 
  <td width="22%" valign="top" class="vncell">NTP time server</td>
  <td width="78%" class="vtable"> <input name="timeservers" type="text" class="formfld" id="timeservers" size="40" value="<?php echo $mono->ntpserver; ?>"> 
  <br> <span class="vexpl">Use a space to separate multiple 
  hosts (only one required). Remember to set up at least one 
  DNS server if you enter a host name here!</span></td>
 </tr>
 <tr> 
  <td width="22%" valign="top">&nbsp;</td>
  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="Save"> 
  </td>
 </tr>
</table>
</form>

<?php } else if ($_POST["action"] == 1) { /* update monowall */ ?>
<?php

  $mod = 0;
  if (mysql_escape_string($_POST["hostname"]) != $mono->hostname) {
    $mono->hostname = mysql_escape_string($_POST["hostname"]);
    $mod = 1;
  }

  if (mysql_escape_string($_POST["domain"]) != $mono->domain) {
    $mono->domain= mysql_escape_string($_POST["domain"]);
    $mod = 1;
  }

  if (mysql_escape_string($_POST["ip"]) != $mono->ip) {
    $mono->ip = mysql_escape_string($_POST["ip"]);
    $mod = 1;
  }

  if (isset($_POST["use_ip"]) && $_POST["use_ip"] == 1 && $mono->use_ip == 0) {
    if (empty($mono->ip)) { echo "You must set Ip address if you want to use it to update m0n0wall...<br/>\n"; die(); }
    $mono->use_ip = 1;
    $mod = 1;
  } else if ($mono->use_ip == 1 && !isset($_POST["use_ip"])) {
    $mono->use_ip = 0;
    $mod = 1;
  }

  if (checkPost("port") && $_POST["port"] != $mono->port) {
    $mod = 1;
    $mono->port = $_POST["port"];
  }
  
  if (checkPost("https") && $_POST["https"] == 1 && $mono->https == 0) {
   $mono->https = 1;
   $mod = 1;
  } else if ($mono->https == 1 && (!checkPost("https") || $_POST["https"] != 1)) {
    $mod = 1;
    $mono->https = 0;
  }
  
  $dns = explode(";", $mono->dnsserver);
  if ($dns[0] != mysql_escape_string($_POST["dns1"])) {
    $dns[0] = mysql_escape_string($_POST["dns1"]);
    $mod = 1;
  }
  if ($dns[1] != mysql_escape_string($_POST["dns2"])) {
    $dns[1] = mysql_escape_string($_POST["dns2"]);
    $mod = 1;
  }
  if ($dns[2] != mysql_escape_string($_POST["dns3"])) {
    $dns[2] = mysql_escape_string($_POST["dns3"]);
    $mod = 1;
  }
  $dnss = $dns[0].";";
  if (!empty($dns[1])) $dnss .= $dns[1].";";
  if (!empty($dns[2])) $dnss .= $dns[2].";";
  $mono->dnsserver = $dnss;
 
  if (isset($_POST["dnsallowoverride"]) && $_POST["dnsallowoverride"] == 1 && $mono->dnsoverride == 0) {
    $mono->dnsoverride = 1;
    $mod = 1;
  } else if ($mono->dnsoverride == 1 && !isset($_POST["dnsallowoverride"])) {
    $mono->dnsoverride = 0;
    $mod = 1;
  }

  if ($mono->username != mysql_escape_string($_POST["username"])) {
    $mono->username = mysql_escape_string($_POST["username"]);
    $mod = 1;
  }

  if (mysql_escape_string($_POST["password"]) != "" && mysql_escape_string($_POST["password"]) == mysql_escape_string($_POST["password2"])) {
    $mono->password = crypt(mysql_escape_string($_POST["password"]));
    $mod = 1;
  }

  if (mysql_escape_string($_POST["timezone"]) != $mono->timezone) {
    $mono->timezone = mysql_escape_string($_POST["timezone"]);
    $mod = 1;
  }

  if ($mono->ntpinterval != mysql_escape_string($_POST["timeupdateinterval"])) {
    $mono->ntpinterval = mysql_escape_string($_POST["timeupdateinterval"]);
    $mod = 1;
  }

  if ($mono->ntpserver != mysql_escape_string($_POST["timeservers"])) {
    $mono->ntpserver = mysql_escape_string($_POST["timeservers"]);
    $mod = 1;
  }

  if ($mod) {
    $mono->update();
    $mono->updateChanged();
    echo "Monowall updated in database.<br/>";
  } else {
    echo "No modification to make in database.<br/>";
  }

 } else if ($_POST["action"] == 3) { /* add new m0n0wall */

  $mono = new Monowall();

  if (isset($_POST["hostname"]) && isset($_POST["domain"])) {
   $mono->hostname = mysql_escape_string($_POST["hostname"]);
   $mono->domain = mysql_escape_string($_POST["domain"]);
    
   if (isset($_POST["ip"])) $mono->ip = mysql_escape_string($_POST["ip"]);
   if (isset($_POST["use_ip"]) && $_POST["use_ip"] == 1) {
     $mono->use_ip = 1;
     if (empty($mono->ip)) {
      echo "Error, you must set IP address if you want to use it!<br/>";
     }
   } else if (!isset($_POST["use_ip"]) || $_POST["use_ip"] == 0) {
     $mono->use_ip = 0;
   }
   if (checkPost("port")) { $mono->port = $_POST["port"]; } else $mono->port = 443;
   if (checkPost("https") && $_POST["https"] == 1) { $mono->https = 1; } else $mono->https = 0;

   $dns = "";
   if (!empty($_POST["dns1"])) $dns .= mysql_escape_string($_POST["dns1"]).";";
   if (!empty($_POST["dns2"])) $dns .= mysql_escape_string($_POST["dns2"]).";";
   if (!empty($_POST["dns3"])) $dns .= mysql_escape_string($_POST["dns3"]).";";
   $mono->dnsserver = $dns;

   if (isset($_POST["dnsallowoverride"]) && $_POST["dnsallowoverride"] == 1 && $mono->dnsoverride == 0) {
     $mono->dnsoverride = 1;
   } else if ($mono->dnsoverride == 1 && !isset($_POST["dnsallowoverride"])) {
     $mono->dnsoverride = 0;
   }
 
   if (!empty($_POST["username"])) $mono->username = mysql_escape_string($_POST["username"]); 
   if (!empty($_POST["password"]) && ($_POST["password"] == $_POST["password2"])) $mono->password = crypt(mysql_escape_string($_POST["password"])); 
   else if ($_POST["password"] != $_POST["password2"]) { echo "Error with password and his confirmation (not the same).<br/>"; }

   if (!empty($_POST["timezone"])) $mono->timezone = mysql_escape_string($_POST["timezone"]);
 
   if (!empty($_POST["timeupdateinterval"])) {
    $mono->ntpinterval = mysql_escape_string($_POST["timeupdateinterval"]);
   }

   if (!empty($_POST["timeservers"])) {
     $mono->ntpserver = mysql_escape_string($_POST["timeservers"]);
   }

   if ($mono->existsDb()) {
    echo "This m0n0wall already exist in database...<br/>\n";
   } else {
    $mono->insert();
    $mono->id = Mysql::getInstance()->getLastId();

    $syslog = new Syslog();
    $syslog->idhost = $mono->id;
    $snmp = new Snmp();
    $snmp->idhost = $mono->id;
    $syslog->insert();
    $mono->idsyslog = Mysql::getInstance()->getLastId();
    $snmp->insert();
    $mono->idsnmp = Mysql::getInstance()->getLastId();

    $lan = new Iface();
    $lan->type = "lan";
    $lan->idhost = $mono->id;

    $wan = new Iface();
    $wan->type = "wan";
    $wan->idhost = $mono->id;

    $lan->insert();
    $wan->insert();


    $mono->update();
    $mono->updateChanged();
    echo "m0n0wall correctly added into database...<br/>\n";
   }
  }
  else {
    echo "Error, you must set hostname & domain name..<br/>\n";
  }

 } else {
  echo "Error in action given in parameters.<br/>";
 } 

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
<br/><a href="hlist.php">Return to m0n0wall list</a><br/>
