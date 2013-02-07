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
 $main->fetchMonoId();
 $main->fetchMonoDetails();

 if (isset($_GET["action"])) $action = $_GET["action"];
 if (isset($_POST["action"])) $action = $_POST["action"];
 if (!isset($action)) $action = 1;

 if (isset($_GET["mid"])) $mid = $_GET["mid"];
 if (isset($_POST["mid"])) $mid = $_POST["mid"];
 if (isset($mid)) { 
   $mono = new Monowall($mid);
   $mono->fetchFromId();
   $mono->fetchSnmp();
   $snmp = $mono->snmp;
 }    
?>

<p class="pgtitle">SNMP: Edit</p>

<?php if ($action == 1) { ?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis;
	endis = !(document.iform.enable.checked || enable_change);
	document.iform.syslocation.disabled = endis;
	document.iform.syscontact.disabled = endis;
	document.iform.rocommunity.disabled = endis;
	document.iform.bindlan.disabled = endis;
}
//-->
</script>
<form action="snmmod.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td width="22%" valign="top" class="vtable">&nbsp;</td>
                  <td width="78%" class="vtable">
<input name="enable" type="checkbox" value="yes" <?php if ($snmp->enable) echo "checked"; ?> onClick="enable_change(false)">
                    <strong>Enable SNMP agent</strong></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncell">System location</td>
                  <td width="78%" class="vtable"> 
                    <input name="syslocation" type="text" class="formfld" id="syslocation" size="40" value="<?=htmlspecialchars($snmp->syslocation);?>"> 
                  </td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncell">System contact</td>
                  <td width="78%" class="vtable"> 
                    <input name="syscontact" type="text" class="formfld" id="syscontact" size="40" value="<?=htmlspecialchars($snmp->syscontact);?>"> 
                  </td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">Community</td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="rocommunity" type="text" class="formfld" id="rocommunity" size="40" value="<?=htmlspecialchars($snmp->rocommunity);?>"> 
                    <br>
                    In most cases, &quot;public&quot; is used here</td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vtable"></td>
                  <td width="78%" class="vtable"> 
                    <input name="bindlan" type="checkbox" value="yes" <?php if ($snmp->bindlan) echo "checked"; ?>> <strong>Bind to LAN interface only</strong>
                    <br>
                    This option can be useful when trying to access the SNMP agent
                    by the LAN interface's IP address through a VPN tunnel terminated on the WAN interface.</td>
                </tr>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="Save" onClick="enable_change(true)"> 
                  </td>
                </tr>
              </table>
<input name="mid" type="hidden" value="<?=$mono->id;?>">
<?php if (isset($mono) && $mono) { ?>
<input name="action" type="hidden" value="3">
<?php } else { ?>         
<input name="action" type="hidden" value="2">     
<?php } ?>
</form>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>

<?php
} else if ($action == 2) /* add */ {

echo "Blop.<br/>";

} else if ($action == 3) /* mod */ {

 if (!isset($snmp)) {
   echo "Error while loading SNMP settings.<br/>";
   die();
 }
 $mod = 0;

 if (checkPost("enable") && $_POST["enable"] == yes && !$snmp->enable) {
  $snmp->enable = 1;
  $mod = 1;
 } else {
  if ($snmp->enable == 1 && !checkPost("enable")) {
   $snmp->enable = 0;
   $mod = 1;
  }
 }

 if (checkPost("bindlan") && $_POST["bindlan"] == yes && !$snmp->bindlan) {
  $snmp->bindlan = 1;
  $mod = 1;
 } else {
  if ($snmp->bindlan == 1 && !checkPost("bindlan")) {
   $snmp->bindlan = 0;
   $mod = 1;
  }
 }

 if (checkPost("syslocation") && $_POST["syslocation"] != $snmp->syslocation) {
  $snmp->syslocation = $_POST["syslocation"];
  $mod = 1;
 }

 if (checkPost("syscontact") && $_POST["syscontact"] != $snmp->syscontact) {
  $snmp->syscontact = $_POST["syscontact"];
  $mod = 1;
 }

 if (checkPost("rocommunity") && $_POST["rocommunity"] != $snmp->rocommunity) {
  $snmp->rocommunity = $_POST["rocommunity"];
  $mod = 1;
 }

 if ($mod) {
  $snmp->update();
  $mono->updateChanged();
  echo "SNMP Settings updated..<br/>";
 } else echo "No settings to update..<br/>";

}

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
