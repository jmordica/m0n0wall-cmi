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

 if (isset($_GET["action"])) $action = $_GET["action"];
 if (isset($_POST["action"])) $action = $_POST["action"];
 if (!isset($action)) $action = 1;

 if (isset($gaid) && $gaid) {
  $main = Main::getInstance();
  $main->fetchGAliases();
  $main->fetchGAliasesDetails();
  if(isset($gaid) && $gaid) {
   $alias = new GAlias($gaid);
   $alias->fetchFromId();
  }
 }

?>
<p class="pgtitle">Global Alias: Edit</p>

<?php if ($action == 1) { 
if (!isset($alias)) $alias = new GAlias();
?>
<script language="JavaScript">
<!--
function typesel_change() {
	switch (document.iform.type.selectedIndex) {
		case 0:	/* host */
			document.iform.address_subnet.disabled = 1;
			document.iform.address_subnet.value = "";
			break;
		case 1:	/* network */
			document.iform.address_subnet.disabled = 0;
			break;
	}
}
//-->
</script>
            <form action="galiasmod.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td valign="top" class="vncellreq">Name</td>
                  <td class="vtable"><input name="name" type="text" class="formfld" id="name" size="40" value="<?=$alias->name;?>"> 
                    <br> <span class="vexpl">The name of the alias may only consist 
                    of the characters a-z, A-Z and 0-9.</span></td>
                </tr>
                <tr> 
                  <td valign="top" class="vncellreq">Type</td>
                  <td class="vtable"> 
                    <select name="type" class="formfld" id="type" onChange="typesel_change()">
                      <option value="host" <?php if (!strpos($alias->address, "/")) echo "selected"; ?>>Host</option>
                      <option value="network" <?php if (strpos($alias->address, "/")) echo "selected"; ?>>Network</option>
                    </select>
                  </td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">Address</td>
<?php if (strpos($alias->address, "/")) { $a = explode('/', $alias->address); $addr = $a[0]; $net = $a[1]; }
      else { $addr = $alias->address; $net = 0; } ?>
                  <td width="78%" class="vtable"><input name="address" type="text" class="formfld" id="address" size="20" value="<?=$addr;?>">
                    / 
                    <select name="address_subnet" class="formfld" id="address_subnet">
                      <?php for ($i = 32; $i >= 1; $i--): ?>
                      <option value="<?=$i;?>" <?php if ($i == $net) echo "selected"; ?>> 
                      <?=$i;?>
                      </option>
                      <?php endfor; ?>
                    </select> <br> <span class="vexpl">The address that this alias 
                    represents.</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable"> <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?=$alias->description;?>"> 
                    <br> <span class="vexpl">You may enter a description here 
                    for your reference (not parsed).</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="Save"> 
		  <?php if (isset($gaid) && $gaid && isset($alias) && $alias) { ?>
 		  <input name="gaid" type="hidden" value="<?=$alias->id;?>">
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

$alias = new GAlias();
$err = 0;

if (checkPost("name"))
  $alias->name = $_POST["name"];
else $err = 1;

if (checkPost("descr"))
  $alias->description = $_POST["descr"];

if (checkPost("type")) {
 if ($_POST["type"] == "host" && checkPost("address")) 
  $alias->address = $_POST["address"];
 else if ($_POST["type"] == "network" && checkPost("address") && checkPost("address_subnet"))
  $alias->address = $_POST["address"]."/".$_POST["address_subnet"];
 else $err = 1;
} else $err = 1;

if (!$err) {
 if ($alias->existsDb()) echo "Alias already in database...<br/>";
 else {
  $alias->insert();
  echo "Alias inserted.<br/>";
 }
} else echo "Error, missing or incorrect field.<br/>";

} else if ($action == 3) /*mod*/ {

 if (!isset($alias)) {
  die("Error while loading Alias object..<br/>");
 }
 $mod = 0;

 if (checkPost("name") && $_POST["name"] != $alias->name) {
  $alias->name = $_POST["name"];
  $mod = 1;
 }

 if (checkPost("descr") && $alias->description != $_POST["descr"]) {
  $alias->description = $_POST["descr"];
  $mod = 1;
 }

 if (checkPost("type") && $_POST["type"] == "host") {
   if (checkPost("address") && $_POST["address"] != $alias->address) {
     $alias->address = $_POST["address"];
     $mod = 1;
   }
 } else if (checkPost("type") && $_POST["type"] == "network") {
  if (checkPost("address") && checkPost("address_subnet") && $_POST["address"]."/".$_POST["address_subnet"] != $alias->address) {
    $alias->address = $_POST["address"]."/".$_POST["address_subnet"];
    $mod = 1;
  }
 } else {
  die("Missing some fields to be complete");
 }

 if ($mod) {
   $alias->update();
   echo "Alias updated.<br/>";
 } else echo "Nothing to update.<br/>";

} else {

}
?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
