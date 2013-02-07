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

 if (isset($_GET["mid"])) $mid = $_GET["mid"];
 if (isset($_POST["mid"])) $mid = $_POST["mid"];
 if (isset($mid)) {
   $mono = new Monowall($mid);
   $mono->fetchFromId();
   $mono->fetchGroups();
   $mono->fetchUsers();
 }

?>

<p class="pgtitle">Groups: List</p>

<?php
if (!isset($mono)) {
?>
<form action="groups.php" method="post">
<table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr>
  <td width="22%" valign="top" class="vncellreq">m0n0wall</td>
  <td width="78%" class="vtable"><select name="mid">
   <?php
     foreach ($main->monowall as $mono) {
      ?><option value="<?php echo $mono->id; ?>"><?php echo $mono->hostname.".".$mono->domain; ?></option>
   <?php }
   ?>
  </select><br><span class="vexpl">Select the m0n0wall which you want to manage groups<br/></span></td>
 </tr>
 <tr>
  <td width="22%" valign="top">&nbsp;</td>
  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="View groups">
  </td>
 </tr>
</table>
</form>
<?php
} else {
?>

<?=$mono->hostname.".".$mono->domain;?> Groups<br/><br/>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr id="frheader">
  <td class="listhdrr"><center>Name</center></td>
  <td class="listhdrr"><center>Description</center></td>
  <td class="listhdrr"><center>Pages</center></td>
  <td class="listhdrr"><center>Edit</center></td>
  <td class="listhdrr"><center>Del</center></td>
 </tr>
<?php
  foreach($mono->group as $group) {
    ?>  <tr>
    <td class="listlr"><center><?php echo $group->name; ?></center></td>
    <td class="listr"><center><?php echo $group->description; ?></center></td>
    <td class="listr"><center><?php echo str_replace(";", "<br/>", $group->pages); ?></center></td>
    <td class="listr"><center><a href="gmod.php?mid=<?=$mono->id;?>&gid=<?php echo $group->id; ?>">X</a></center></td>
    <td class="listr"><center><a href="grm.php?mid=<?=$mono->id;?>&gid=<?php echo $group->id; ?>">X</a></center></td>
   </tr>
   <?php
  }
?></table><br/><a href="gmod.php?mid=<?=$mono->id;?>">Add new group</a><?php
}

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
