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
 require_once("./lib/html.lib.php");
 require_once("./lib/autoload.lib.php");

 /* connect to the database: */
 if (!Mysql::getInstance()->connect())
 { echo "Cannot connect to mysql database<br/>\n"; die(); }

 /* sanitize _GET and _POST */
 sanitizeArray($_GET);
 sanitizeArray($_POST);

 $main = Main::getInstance();
 $main->fetchMonoId();
 $main->fetchMonoDetails();

 if (isset($_GET["mid"])) $mid = $_GET["mid"];
 if (isset($_POST["mid"])) $mid = $_POST["mid"];
 if (isset($_GET["vid"])) $vid = $_GET["vid"];
 if (isset($_POST["vid"])) $vid = $_POST["vid"];

 if (isset($_GET["action"])) $action = $_GET["action"];
 if (isset($_POST["action"])) $action = $_POST["action"];
 if (!isset($action)) $action = 1;

 if (isset($mid)) {
   $mono = new Monowall($mid); 
   $mono->fetchFromId();
   $mono->fetchIfaces();
   $mono->fetchIfacesDetails();
   $mono->fetchVlans();
   $mono->fetchVlansDetails();
   $mono->fetchHw();
   if (isset($vid)) {
     $vlan = new Vlan($vid);
     $vlan->fetchFromId();
   }
 }   
?>
<p class="pgtitle">VLANs: Edit</p>

<?php if ($action == 1) { ?>

<form action="vlmod.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
				<tr>
                  <td width="22%" valign="top" class="vncellreq">Parent interface</td>
                  <td width="78%" class="vtable"> 
                    <select name="if" class="formfld">
                      <?php
					  foreach ($mono->hwInt as $if): ?>
                      <option value="<?=$if->id;?>" <?php if ($vlan->if == $if->name) echo "selected"; ?>> 
                      <?=htmlspecialchars($if->name . " ($if->mac)");?>
                      </option>
                      <?php endforeach; ?>
                    </select></td>
                </tr>
				<tr>
                  <td valign="top" class="vncellreq">VLAN tag </td>
                  <td class="vtable">
                    <?=$mandfldhtml;?><input name="tag" type="text" class="formfld" id="tag" size="6" value="<?=htmlspecialchars($vlan->tag);?>">
                    <br>
                    <span class="vexpl">802.1Q VLAN tag (between 1 and 4094) </span></td>
			    </tr>
				<tr>
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable"> 
                    <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?=htmlspecialchars($vlan->description);?>">
                    <br> <span class="vexpl">You may enter a description here
                    for your reference (not parsed).</span></td>
                </tr>
                <tr>
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="Save">
<?php if (isset($vid) && $vid && isset($vlan) && $vlan) { ?>
   <input name="vid" type="hidden" value="<?=$vlan->id;?>">
   <input name="action" type="hidden" value="3">
<?php } else { ?>
   <input name="action" type="hidden" value="2">
<?php } ?>
		    <input name="mid" type="hidden" value="<?=$mono->id;?>">
                  </td>
                </tr>
              </table>
</form>


<?php

} else if ($action == 2) /* add */ {

$vlan = new Vlan();
$vlan->idhost = $mono->id;
if (checkPost("if")) {
  
  $vlan->if = "";
  foreach ($mono->hwInt as $if) {
    if ($if->id == $_POST["if"]) {
      $vlan->if = $if->name;
      break;
    }
  }
  if ($vlan->if != "" && checkPost("tag")) {

    $vlan->tag = $_POST["tag"];
    if (checkPost("descr"))
      $vlan->description = $_POST["descr"];

    if ($vlan->existsDb()) {
      echo "VLAN already in database...<br/>";
    } else {
      $vlan->insert();
      $mono->updateChanged();
      echo "VLAN added in database...<br/>";
    }
  } else {
   echo "Error, incorrect field in the form.<br/>";
  }
}

} else if ($action == 3) /* mod */ {

 if (isset($vlan)) {
  $mod = 0;

  if (checkPost("if")) {
    foreach ($mono->hwInt as $if) {
     if ($if->id == $_POST["if"]) {
      if ($if->name != $vlan->if) {
       $vlan->if = $if->name;
       $mod = 1;
       break;
      }
     }
    }
  }
  if (checkPost("tag") && $_POST["tag"] != $vlan->tag) {
    $vlan->tag = $_POST["tag"];
    $mod = 1;
  }  
  if (checkPost("descr") && $_POST["descr"] != $vlan->description) {
    $vlan->description = $_POST["descr"];
    $mod = 1;
  }
  if ($mod) {
    
    $vlan->update();
    $mono->updateChanged();
    echo "VLAN updated...<br/>";
  }

 } else {

  echo "VLAN not found.<br/>";
 }

}

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
