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

 /* connect to the database: */
 if (!Mysql::getInstance()->connect())
 { echo "Cannot connect to mysql database<br/>\n"; die(); }
 
 /* sanitize _GET and _POST */
 sanitizeArray($_GET);
 sanitizeArray($_POST);

 if (isset($_GET["pid"])) $pid = $_GET["pid"];
 if (isset($_POST["pid"])) $pid = $_POST["pid"];
 
 if (isset($_GET["mid"])) $mid = $_GET["mid"];
 if (isset($_POST["mid"])) $mid = $_POST["mid"];
     
 if (isset($_GET["action"])) $action = $_GET["action"];
 if (isset($_POST["action"])) $action = $_POST["action"];
 if (!isset($action)) $action = 1;

 if (isset($mid) && $mid) {
  $mono = new Monowall($mid);
  $mono->fetchFromId();
  $mono->fetchIfaces();
  $mono->fetchIfacesDetails();
  $mono->fetchProxyArp();
  $mono->fetchProxyArpDetails();
  if(isset($pid) && $pid) {
   $pa = new ProxyArp($pid);
   $pa->fetchFromId();
  }
 } else  die("No monowall selected");
 
?>
<p class="pgtitle">ProxyARP: Edit</p>

<?php if ($action == 1) {
if (!isset($pa)) $pa = new ProxyArp();
?>

<script language="JavaScript">
<!--
function typesel_change() {
    switch (document.iform.type.selectedIndex) {
        case 0: // single
            document.iform.subnet.disabled = 0;
            document.iform.subnet_bits.disabled = 1;
            document.iform.range_from.disabled = 1;
            document.iform.range_to.disabled = 1;
            break;
        case 1: // network
            document.iform.subnet.disabled = 0;
            document.iform.subnet_bits.disabled = 0;
            document.iform.range_from.disabled = 1;
            document.iform.range_to.disabled = 1;
            break;
        case 2: // range
            document.iform.subnet.disabled = 1;
            document.iform.subnet_bits.disabled = 1;
            document.iform.range_from.disabled = 0;
            document.iform.range_to.disabled = 0;
            break;
    }
}
//-->
</script>
            <form action="pamod.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">Interface</td>
                  <td width="78%" class="vtable">
		  <select name="interface" class="formfld">
<?php $interfaces = array('wan' => 'WAN', 'lan' => 'LAN', 'pptp' => 'PPTP');
         foreach($mono->ifaces as $if) { if ($if->type == "opt") $interfaces[$if->type.$if->num] = $if->description; }
         foreach ($interfaces as $iface => $ifacename): ?>
           <option value="<?=$iface;?>" <?php if ($iface == $pa->if) echo "selected"; ?>>
            <?=htmlspecialchars($ifacename);?>
           </option>
<?php endforeach; ?>
                    </select> </td>
                </tr>
                <tr> 
                  <td valign="top" class="vncellreq">Network</td>
                  <td class="vtable">
                    <table border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>Type:&nbsp;&nbsp;</td>
						<td></td>
<?php
 $ar = explode('/', $pa->network);
 $address = $ar[0];
 $subnet = $ar[1];
?>
                        <td><select name="type" class="formfld" onChange="typesel_change()">
                            <option value="single" <?php if (empty($pa->from) && $subnet == 32) echo "selected"; ?>> 
                            Single address</option>
                            <option value="network" <?php if (empty($pa->from) && $subnet != 32) echo "selected"; ?>> 
                            Network</option>
                            <option value="range" <?php if (!empty($pa->from)) echo "selected"; ?>> 
                            Range</option>
                          </select></td>
                      </tr>
                      <tr> 
                        <td>Address:&nbsp;&nbsp;</td>
						<td></td>
                        <td><input name="subnet" type="text" class="formfld" id="subnet" size="20" value="<?=htmlspecialchars($address);?>">
                  / 
                          <select name="subnet_bits" class="formfld" id="select">
                            <?php for ($i = 31; $i >= 0; $i--): ?>
                            <option value="<?=$i;?>" <?php if ($i == $subnet) echo "selected"; ?>>
                            <?=$i;?>
                      </option>
                            <?php endfor; ?>
                      </select>
 </td>
                      </tr>
                      <tr> 
                        <td>Range:&nbsp;&nbsp;</td>
						<td></td>
                        <td><input name="range_from" type="text" class="formfld" id="range_from" size="20" value="<?=htmlspecialchars($pa->from);?>">
- 
                          <input name="range_to" type="text" class="formfld" id="range_to" size="20" value="<?=htmlspecialchars($pa->to);?>">                          
                          </td>
                      </tr>
                    </table>
                  </td>
                </tr>
				<tr>
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable"> 
                    <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?=htmlspecialchars($pa->description);?>">
                    <br> <span class="vexpl">You may enter a description here
                    for your reference (not parsed).</span></td>
                </tr>
                <tr>
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="Save">
		    <input name="mid" type="hidden" value="<?=$mono->id;?>">
                    <?php if (isset($pid) && $pid && isset($pa) && $pa) { ?>
                    <input name="pid" type="hidden" value="<?=$pa->id;?>">
                    <input name="action" type="hidden" value="3">
                    <?php } else { ?>
                    <input name="action" type="hidden" value="2">
                    <?php } ?>
                  </td>
                </tr>
              </table>
</form>
<script language="JavaScript">
<!--
typesel_change();
//-->
</script>

<?php } else if ($action == 2) /*add*/ {

$pa = new ProxyArp();
$pa->idhost = $mono->id;

if (checkPost("interface")) {
  $pa->if = $_POST["interface"];
}

if (checkPost("type")) {
  if ($_POST["type"] == "single" && checkPost("subnet")) {

    $pa->network = $_POST["subnet"]."/32";
    
  }
  else if ($_POST["type"] == "network" && checkPost("subnet") && checkPost("subnet_bits")) {

    $pa->network = $_POST["subnet"]."/".$_POST["subnet_bits"];

  } else if ($_POST["type"] == "range" && checkPost("range_from") && checkPost("range_to")) {

    $pa->from = $_POST["range_from"];
    $pa->to = $_POST["range_to"];
  }
}

if (checkPost("descr")) {
  $pa->description = $_POST["descr"];
}

if ($pa->existsDb()) {
 die("Error, proxyarp already exist in database...<br/>");
}

$pa->insert();
$mono->updateChanged();
echo "Proxy Arp inserted in database";

} else if ($action == 3) /*mod*/ {

$mod = 0;

if (checkPost("interface") && $_POST["interface"] != $pa->if) {
  $pa->if = $_POST["interface"];
  $mod = 1;
}

if (checkPost("type")) {
  if ($_POST["type"] == "single" && checkPost("subnet") && $pa->network != $_POST["subnet"]."/32") {

    $pa->network = $_POST["subnet"]."/32";
    $pa->from = "";
    $pa->to = "";
    $mod = 1;
    
  }
  else if ($_POST["type"] == "network" && checkPost("subnet") && checkPost("subnet_bits") && $pa->network != $_POST["subnet"]."/".$_POST["subnet_bits"]) {

    $pa->network = $_POST["subnet"]."/".$_POST["subnet_bits"];
    $pa->from = "";
    $pa->to = "";
    $mod = 1;

  } else if ($_POST["type"] == "range" && checkPost("range_from") && checkPost("range_to") && ($pa->from != $_POST["range_from"] || $pa->to != $_POST["range_to"])) {

    $pa->network = "";
    $pa->from = $_POST["range_from"];
    $pa->to = $_POST["range_to"];
    $mod = 1;
  }
}

if (checkPost("descr") && $pa->description != $_POST["descr"]) {
  $pa->description = $_POST["descr"];
  $mod = 1;
}

if ($mod) {
  $pa->update();
  $mono->updateChanged();
  echo "ProxyARP updated.<br/>";
} else echo "Nothing to update..<br/>";

} else {

}
?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
