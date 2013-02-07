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


 if (isset($_GET["action"])) $action = $_GET["action"];
 if (isset($_POST["action"])) $action = $_POST["action"];
 if (!isset($action)) $action = 1;

  if (isset($_GET["mid"])) $mid = $_GET["mid"];
  if (isset($_POST["mid"])) $mid = $_POST["mid"];
  if (isset($_GET["gid"])) $gid = $_GET["gid"];
  if (isset($_POST["gid"])) $gid = $_POST["gid"];
  if (isset($mid) && $mid) {
   $mono = new Monowall($mid);
   $mono->fetchFromId();

   $g = new Group($gid);
   $g->fetchFromId();
  } else die("No group or monowall found.");

?>
<p class="pgtitle">Group: Edit</p>

<?php if ($action == 1) { ?>

<form action="gmod.php" method="post" name="iform" id="iform">
<table width="100%" border="0" cellpadding="6" cellspacing="0">
            <tr> 
              <td width="22%" valign="top" class="vncellreq">Group name</td>
              <td width="78%" class="vtable"> 
                <input name="groupname" type="text" class="formfld" id="groupname" size="20" value="<?=$g->name;?>"> 
                </td>
            </tr>
            <tr> 
              <td width="22%" valign="top" class="vncell">Description</td>
              <td width="78%" class="vtable"> 
                <input name="description" type="text" class="formfld" id="description" size="20" value="<?=$g->description;?>">
                <br>
                Group description, for your own information only</td>
            </tr>
            <tr>
			  	<td colspan="4"><br>&nbsp;Select that pages that this group may access.  Members of this group will be able to perform all actions that<br>&nbsp; are possible from each individual web page.  Ensure you set access levels appropriately.<br><br>
			  	<span class="vexpl"><span class="red"><strong>&nbsp;Note: </strong></span>Pages 
          marked with an * are strongly recommended for every group.</span>
			  	</td>
				</tr>
            <tr>
              <td colspan="2">
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td class="listhdrr">&nbsp;</td>
                <td class="listhdrr">Page</td>
              </tr>
              <?php 
              $pages = explode (';',$g->pages); 
              foreach ($group_pages as $fname => $title) {
              	$identifier = str_replace('.php','',$fname);
              	?>
              	<tr><td class="listlr">
              	<input name="<?=$identifier?>" type="checkbox" id="<?=$identifier?>" value="yes" <?php if (in_array($fname,$pages)) echo "checked"; ?>></td>
              	<td class="listr"><?=$title?></td>
              	</tr>
              	<?
              } ?>
              </table>
              </td>
            </tr>
            <tr> 
              <td width="22%" valign="top">&nbsp;</td>
              <td width="78%"> 
                <input name="save" type="submit" class="formbtn" value="Save"> 
		<input name="mid" type="hidden" value="<?=$mono->id;?>">
		<?php if (isset($gid) && $gid && isset($g) && $g) { ?>
		   <input name="gid" type="hidden" value="<?=$g->id;?>">
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

$g  = new Group();
$g->idhost = $mono->id;

if (checkPost("groupname")) {
 $g->name = $_POST["groupname"];
 if (checkPost("description")) {
  $g->description = $_POST["description"];
 }
 foreach ($group_pages as $phpfile => $title) {
  $fname = str_replace('.php','',$phpfile);
  if (checkPost($fname) && $_POST[$fname] == "yes") {
    $g->pages .= $phpfile.";";
  }
 } 

 if ($g->existsDb()) {
   echo "Group already exist...<br/>";
 }
 else {
   $g->insert();
   $mono->updateChanged();
   echo "Group Inserted.<br/>";
 }

} else {
 echo "Missing field<br/>";
}

} else if ($action == 3) /* mod */ {

if (isset($g) && $g) {
  $mod = 0;
  if (checkPost("grouname") && $_POST["groupname"] != $g->name) {
    $g->name = $_POST["groupname"];
    $mod = 1;
  }
  if (checkPost("description") && $_POST["description"] != $g->description) {
    $g->description = $_POST["description"];
    $mod = 1;
  }
  $pages = explode (';',$g->pages);
  foreach ($group_pages as $phpfile => $title) {
    $fname = str_replace('.php','',$phpfile);
    if (checkPost($fname)) {
      if ($_POST[$fname] == "yes") {
      	if (!in_array($phpfile, $pages)) {
	 $g->pages .= $phpfile.";";
	 $pages[] = $phpfile;
	 $mod = 1;
	}
      } else {
	if (in_array($phpfile, $pages)) {
	  $g->pages = "";
	  foreach ($pages as $k => $p) {
	    if ($p != $phpfile && !empty($p)) $g->pages .= $p.";";
	    else unset($pages[$k]);
	  }
	  $mod = 1;
	}
      }
    } else {
	if (in_array($phpfile, $pages)) {
	  $g->pages = "";
	  foreach ($pages as $k => $p) {
	    if ($p != $phpfile && !empty($p)) $g->pages .= $p.";";
	    else unset($pages[$k]);
	  }
	  $mod = 1;
	}
    }
  }  
  if ($mod) {
    $g->update();
     $mono->updateChanged();
    echo "Group updated in database...<br/>";
  } else echo "Nothing to update...<br/>";
}

}

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
