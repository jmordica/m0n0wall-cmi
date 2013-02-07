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
 }    
?>

<p class="pgtitle">SNMP: View</p>

<?php
if (!isset($mono)) {
?>
<form action="snmp.php" method="post">
<table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr>
  <td width="22%" valign="top" class="vncellreq">m0n0wall</td>
  <td width="78%" class="vtable"><select name="mid">
   <?php
     foreach ($main->monowall as $mono) {
      ?><option value="<?php echo $mono->id; ?>"><?php echo $mono->hostname.".".$mono->domain; ?></option>
   <?php }
   ?>
  </select><br><span class="vexpl">Select the m0n0wall which you want to manage SNMP settings<br/></span></td>
 </tr>
 <tr>
  <td width="22%" valign="top">&nbsp;</td>
  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="View SNMP">
  </td>
 </tr>
</table>
</form>
<?php
} else {
?>
<?=$mono->hostname.".".$mono->domain;?> SNMP<br/><br/>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr id="frheader">
  <td class="listhdrr"><center>Enable?</center></td>
  <td class="listhdrr"><center>Location</center></td>
  <td class="listhdrr"><center>Contact</center></td>
  <td class="listhdrr"><center>RO Community</center></td>
  <td class="listhdrr"><center>Bind lan</center></td>
  <td class="listhdrr"><center>Edit</center></td>
<!--  <td class="listhdrr"><center>Del</center></td>-->
 </tr>
    <tr>
    <td class="listlr"><center><?php echo $mono->snmp->enable; ?></center></td>
    <td class="listr"><center><?php echo $mono->snmp->syslocation; ?></center></td>
    <td class="listr"><center><?php echo $mono->snmp->syscontact; ?></center></td>
    <td class="listr"><center><?php echo $mono->snmp->rocommunity; ?></center></td>
    <td class="listr"><center><?php echo $mono->snmp->bindlan; ?></center></td>
    <td class="listr"><center><a href="snmmod.php?mid=<?=$mono->id;?>">X</a></center></td>
<!--    <td class="listr"><center><a href="snmrm.php?gid=<?php echo $mono->snmp->id; ?>">X</a></center></td>-->
   </tr>
</table><?php
}

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
