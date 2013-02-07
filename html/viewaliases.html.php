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
   $mono->fetchAlias();
   $mono->fetchAliasDetails();
 }

?>
<p class="pgtitle">Aliases: List</p>

<?php
if (!isset($mono)) {
?>
<form action="viewaliases.php" method="post">
<table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr>
  <td width="22%" valign="top" class="vncellreq">m0n0wall</td>
  <td width="78%" class="vtable"><select name="mid">
   <?php
     foreach ($main->monowall as $mono) {
      ?><option value="<?php echo $mono->id; ?>"><?php echo $mono->hostname.".".$mono->domain; ?></option>
   <?php }
   ?>
  </select><br><span class="vexpl">Select the m0n0wall which you want to manage aliases<br/></span></td>
 </tr>
 <tr>
  <td width="22%" valign="top">&nbsp;</td>
  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="View aliases">
  </td>
 </tr>
</table>
</form>
<?php
} else {
?>
<?=$mono->hostname.".".$mono->domain;?> local aliases<br/><br/>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr id="frheader">
  <td class="listhdrr"><center>Name</center></td>
  <td class="listhdrr"><center>Address</center></td>
  <td class="listhdrr"><center>Description</center></td>
  <td class="listhdrr"><center>Edit</center></td>
  <td class="listhdrr"><center>Del</center></td>
 </tr>
<?php
  foreach($mono->alias as $alias) {
    ?>  <tr>
    <td class="listlr"><center><?php echo $alias->name; ?></center></td>
    <td class="listr"><center><?php echo $alias->address; ?></center></td>
    <td class="listr"><center><?php echo empty($alias->description)?"-":$alias->description; ?></center></td>
    <td class="listr"><center><a href="aliasmod.php?mid=<?=$mono->id;?>&aid=<?php echo $alias->id; ?>">X</a></center></td>
    <td class="listr"><center><a href="aliasrm.php?mid=<?=$mono->id;?>&aid=<?php echo $alias->id; ?>">X</a></center></td>
   </tr>
   <?php
  }
?></table><br/><a href="aliasmod.php?mid=<?=$mono->id;?>">Add new alias</a><?php
}

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
