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

 $main = Main::getInstance();

 if (isset($_GET["mid"])) $mid = $_GET["mid"]; 
 if (isset($_POST["mid"])) $mid = $_POST["mid"];

 if (isset($_GET["rid"])) $rid = $_GET["rid"]; 
 if (isset($_POST["rid"])) $rid = $_POST["rid"];

 if (isset($_GET["action"])) $action = $_GET["action"];
 if (isset($_POST["action"])) $action = $_POST["action"];
 if (!isset($action)) $action = 1;

 if (isset($mid)) { 
   $mono = new Monowall($mid);
   $mono->fetchFromId();
   $mono->fetchIfaces();
   $mono->fetchIfacesDetails();
   $mono->fetchRoutes();
   $mono->fetchRoutesDetails();
   if (isset($rid)) {
     $ro = new StaticRoute($rid);
     $ro->fetchFromId();
   }
 }
?>
<p class="pgtitle">Static Route: Edit</p>

<?php
if ($action == 1) {
 if (!isset($ro)) $ro = new StaticRoute();
 ?>

 <form action="srmod.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">Interface</td>
                  <td width="78%" class="vtable">
					<select name="interface" class="formfld">
   <?php $interfaces = array('wan' => 'WAN', 'lan' => 'LAN', 'pptp' => 'PPTP');
         foreach($mono->ifaces as $if) { if ($if->type == "opt") $interfaces[$if->type.$if->num] = $if->description; }
         foreach ($interfaces as $iface => $ifacename): ?>
           <option value="<?=$iface;?>" <?php if ($iface == $ro->if) echo "selected"; ?>>
            <?=htmlspecialchars($ifacename);?>
           </option>
       <?php endforeach; ?>
                    </select> <br>
                    <span class="vexpl">Choose which interface this route applies to.</span></td>
                </tr>
                <tr>
                  <td width="22%" valign="top" class="vncellreq">Destination network</td>
                  <td width="78%" class="vtable"> 
<?php
 if (isset($ro)) {
   if ($ro->network == "") $ro->network = "/32";
   $netw = explode('/', $ro->network);
   $net = $netw[0];
   $sub = $netw[1];
 }
 else { $net = ""; $sub = 32; }
?>
                    <input name="network" type="text" class="formfld" id="network" size="20" value="<?=htmlspecialchars($net);?>"> 
				  / 
                    <select name="network_subnet" class="formfld" id="network_subnet">
                      <?php for ($i = 32; $i >= 1; $i--): ?>
                      <option value="<?=$i;?>" <?php if ($i == $sub) echo "selected"; ?>>
                      <?=$i;?>
                      </option>
                      <?php endfor; ?>
                    </select>
                    <br> <span class="vexpl">Destination network for this static route</span></td>
                </tr>
				<tr>
                  <td width="22%" valign="top" class="vncellreq">Gateway</td>
                  <td width="78%" class="vtable"> 
                    <input name="gateway" type="text" class="formfld" id="gateway" size="40" value="<?=htmlspecialchars($ro->gateway);?>">
                    <br> <span class="vexpl">Gateway to be used to reach the destination network</span></td>
                </tr>
				<tr>
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable"> 
                    <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?=htmlspecialchars($ro->description);?>">
                    <br> <span class="vexpl">You may enter a description here
                    for your reference (not parsed).</span></td>
                </tr>
                <tr>
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="Save">
   <input name="mid" type="hidden" value="<?=$mono->id;?>">
<?php if (isset($rid) && $rid && isset($ro) && $ro) { ?>
   <input name="rid" type="hidden" value="<?=$ro->id;?>">
   <input name="action" type="hidden" value="3">
<?php } else { ?>
   <input name="action" type="hidden" value="2">
<?php } ?>
                  </td>
                </tr>
              </table>
</form>


<?php
} else if ($action == 2) /* add */ {

 $ro = new StaticRoute();
 $ro->idhost = $mono->id;
 if (checkPost("interface") && checkPost("network") && checkPost("network_subnet") && checkPost("gateway")) {
   $ro->if = $_POST["interface"];
   $ro->network = $_POST["network"]."/".$_POST["network_subnet"];
   $ro->gateway = $_POST["gateway"];
   if (checkPost("descr")) {
    $ro->description = $_POST["descr"];
   }
   if ($ro->existsDb()) {
    echo "Route already in database<br/>";
   } else{
     $ro->insert();
     $mono->updateChanged();
     echo "Rule inserted.<br/>";
   }
 
 } else {
  echo "Missing parameter in form.<br/>";
 }

} else if ($action == 3) /* mod */ {

 if (checkPost("interface") && checkPost("network") && checkPost("network_subnet") && checkPost("gateway")) {
   $mod = 0;
   if ($ro->if != $_POST["interface"]) {
     $ro->if = $_POST["interface"];
     $mod = 1;
   }
   if ($ro->network != $_POST["network"]."/".$_POST["network_subnet"]) {
     $ro->network = $_POST["network"]."/".$_POST["network_subnet"];
     $mod = 1;
   }
   if ($ro->gateway != $_POST["gateway"]) {
     $ro->gateway = $_POST["gateway"];
     $mod = 1;
   }
   if (checkPost("descr") && $ro->description != $_POST["descr"]) {
    $ro->description = $_POST["descr"];
    $mod = 1;
   }
   if ($mod) {
     $ro->update();
     $mono->updateChanged();
     echo "Route updated in database<br/>";
   } else {
     echo "No modification to make..<br/>";
   }
 } else {
   echo "Missing parameter in form.<br/>";
 }

}

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
