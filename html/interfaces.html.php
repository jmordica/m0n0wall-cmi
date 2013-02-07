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
   $mono->fetchIfaces();
   $mono->fetchIfacesDetails();
 }

?>

<p class="pgtitle">Interfaces: List</p>

<?php
if (!isset($mono)) {
?>
<form action="interfaces.php" method="post">
<table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr>
  <td width="22%" valign="top" class="vncellreq">m0n0wall</td>
  <td width="78%" class="vtable"><select name="mid">
   <?php
     foreach ($main->monowall as $mono) {
      ?><option value="<?php echo $mono->id; ?>"><?php echo $mono->hostname.".".$mono->domain; ?></option>
   <?php }
   ?>
  </select><br><span class="vexpl">Select the m0n0wall which you want to manage interfaces<br/></span></td>
 </tr>
 <tr>
  <td width="22%" valign="top">&nbsp;</td>
  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="View interfaces">
  </td>
 </tr>
</table>
</form>
<?php
} else {
?>
<?=$mono->hostname.".".$mono->domain;?> Interfaces<br/><br/>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr id="frheader">
  <td class="listhdrr"><center>Name</center></td>
  <td class="listhdrr"><center>Iface</center></td>
  <td class="listhdrr"><center>ENA</center></td>
  <td class="listhdrr"><center>IP</center></td>
  <td class="listhdrr"><center>Subnet</center></td>
  <td class="listhdrr"><center>GW</center></td>
  <td class="listhdrr"><center>Media</center></td>
  <td class="listhdrr"><center>Mopts</center></td>
  <td class="listhdrr"><center>DHCP</center></td>
  <td class="listhdrr"><center>Bridge</center></td>
  <td class="listhdrr"><center>MTU</center></td>
  <td class="listhdrr"><center>SpoofMac</center></td>
  <td class="listhdrr"><center>Edit</center></td>
  <td class="listhdrr"><center>Del</center></td>
 </tr>
<?php
  foreach($mono->ifaces as $if) {
    ?>  <tr>
    <td class="listlr"><center><?php echo $if->type; echo ($if->type=="opt")?$if->num:""; ?></center></td>
    <td class="listr"><center><?php echo $if->if; ?></center></td>
    <td class="listr"><center><?php echo $if->enable; ?></center></td>
    <td class="listr"><center><?php echo empty($if->ipaddr)?"-":$if->ipaddr; ?></center></td>
    <td class="listr"><center><?php echo empty($if->subnet)?"-":$if->subnet; ?></center></td>
    <td class="listr"><center><?php echo empty($if->gateway)?"-":$if->gateway; ?></center></td>
    <td class="listr"><center><?php echo empty($if->media)?"-":$if->media; ?></center></td>
    <td class="listr"><center><?php echo empty($if->mediaopt)?"-":$if->mediaopt; ?></center></td>
    <td class="listr"><center><?php echo empty($if->dhcp)?"-":$if->dhcp; ?></center></td>
    <td class="listr"><center><?php echo empty($if->bridge)?"-":$if->bridge;; ?></center></td>
    <td class="listr"><center><?php echo empty($if->mtu)?"-":$if->mtu; ?></center></td>
    <td class="listr"><center><?php echo empty($if->spoofmac)?"-":$if->spoofmac; ?></center></td>
    <td class="listr"><center><a href="ifmod.php?mid=<?php echo $mono->id; ?>&iid=<?php echo $if->id; ?>">X</a></center></td>
    <td class="listr"><center><?php if ($if->type == "opt") { ?><a href="ifrm.php?mid=<?php echo $mono->id; ?>&iid=<?php echo $if->id; ?>"><?php } ?>X<?php if ($if->type == "opt") { ?></a><?php } ?></center></td>
   </tr>
   <?php
  }
?></table><br/><a href="ifmod.php?mid=<?=$mono->id;?>">Add new interface</a><?php
}

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
