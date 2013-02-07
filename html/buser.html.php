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

 if ($mid) {
  $mono = new Monowall($mid);
  $mono->fetchFromId();
 }
 $main->fetchBusers();
?>
<p class="pgtitle">m0n0wall: Backup user</p>

<?php if (!isset($_POST["action"])) { ?>

<form action="buser.php" method="post">
 <input type="hidden" name="action" value="1">
 <input type="hidden" name="mid" value="<?php echo $mid; ?>">
 <table width="100%" border="0" cellpadding="6" cellspacing="0">
  <tr> 
   <td width="22%" valign="top" class="vncellreq">Backup user</td>
   <td width="78%" class="vtable"><select name="buser" id="buser"><?php
     foreach ($main->busers as $buser) {
       ?><option value="<?php echo $buser->id; ?>" <?php if ($buser->id == $mono->idbuser) echo "selected"; ?>><?php echo $buser->login; ?></option><?php
     }
    ?> 
     </select>
     <br/> <span class="vexpl">Select the backup user to use together with <?php echo $mono->hostname.".".$mono->domain; ?><br/></span></td>
  </tr>
  <tr>
   <td width="22%" valign="top">&nbsp;</td>
   <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="Save">
   </td>
  </tr>
 </table>
</form>

<?php } else if ($_POST["action"] == 1) {

  $bu = new Buser($_POST["buser"]);
  if (!$bu->fetchFromId()) {
    echo "Error cannot found buser with id=".$bu->id."<br/>\n";
    die();
  }
  $mono->idbuser = $bu->id;
  $mono->update();
  
  echo $mono->hostname.".".$mono->domain; ?> has now <?php echo $bu->login; ?> as backup user.<br/>

  <br/><a href="hlist.php">Return to m0n0wall list</a><br/>

<?php } ?>
  
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
