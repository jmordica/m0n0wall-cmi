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
?>

<p class="pgtitle">m0n0wall: List</p>

<table width="100%" border="0" cellpadding="0" cellspacing="0"
 <tr id="frheader">
  <td class="listhdrr"><center>Hostname</center></td>
  <td class="listhdrr"><center>Domain</center></td>
  <td class="listhdrr"><center>DNS Servers</center></td>
  <td class="listhdrr"><center>Settings</center></td>
  <td class="listhdrr"><center>Backup User</center></td>
  <td class="listhdrr"><center>HW</center></td>
  <td class="listhdrr"><center>Edit</center></td>
  <td class="listhdrr"><center>Del</center></td>
  <td class="listhdrr"><center>Status</center></td>
 </tr>
<?php
 foreach($main->monowall as $mono) {
  ?><tr>
     <td class="listlr"><center><?php echo $mono->hostname; ?><br/><?php if (!empty($mono->fversion)) { ?>(ver. <?php echo $mono->fversion; ?>)<?php } ?></center></td>
     <td class="listr"><center><?php echo $mono->domain; ?></center></td>
     <td class="listr"><?php if (!empty($mono->dnsserver)) { echo str_replace(";", "<br/>", $mono->dnsserver); } else { echo "-"; } ?></td>
     <td class="listr"><center>
			<a href="viewfw.php?mid=<?php echo $mono->id; ?>">Firewall</a><br/>
			<a href="viewnat.php?mid=<?php echo $mono->id; ?>">NAT</a><br/>
			<a href="viewaliases.php?mid=<?php echo $mono->id; ?>">Aliases</a><br/>
			<a href="users.php?mid=<?php echo $mono->id; ?>">Users</a><br/>
			<a href="groups.php?mid=<?php echo $mono->id; ?>">Groups</a><br/>
			<a href="interfaces.php?mid=<?php echo $mono->id; ?>">Interfaces</a><br/>
			<a href="proxyarp.php?mid=<?php echo $mono->id; ?>">ProxyARP</a><br/>
			<a href="sroutes.php?mid=<?php echo $mono->id; ?>">Static Routes</a><br/>
			<a href="vlans.php?mid=<?php echo $mono->id; ?>">VLANs</a><br/>
			<a href="syslog.php?mid=<?php echo $mono->id; ?>">Syslog</a><br/>
			<a href="snmp.php?mid=<?php echo $mono->id; ?>">SNMP</a><br/></center></td>
     <td class="listr"><center><a href="buser.php?mid=<?php echo $mono->id; ?>"><?php
      if ($mono->idbuser != -1) { 
       $mono->fetchBuser();
       echo $mono->buser->login;
      } else echo "Assign";
     ?></center></td>
     <td class="listr"><center><a href="hwupdate.php?mid=<?php echo $mono->id; ?>">Update HW List</a></center></td>
     <td class="listr"><center><a href="hmod.php?mid=<?php echo $mono->id; ?>">X</a></center></td>
     <td class="listr"><center><a href="hrm.php?mid=<?php echo $mono->id; ?>">X</a></center></td>
     <td class="listr"><center><?php
	if ($mono->lastchange < $mono->changed) { ?><a href="save2mono.php?mid=<?=$mono->id;?>"><img border="0" alt="CLICK TO SAVE!" src="img/bred.png"/></a><?php }
        else if ($mono->changed == 0 || $mono->lastchange >= $mono->changed) { ?><img alt="no changes" src="img/bgreen.png"/><?php }
     ?><br/><a href="luupdate.php?mid=<?=$mono->id;?>">refresh</a></center></td>
    </tr>
  <?php
 } 
?>
</table>
<a href="luupdate.php">Check last update time of all m0n0wall</a> (this could take a long time if you have many devices)
<br/><br/>
Warning: if you remove a m0n0wall, ALL object linked to this m0n0wall will be deleted as well!
<br/><br/>
<a href="hmod.php?action=2">Add new m0n0wall</a> | <a href="import.php">Import new m0n0wall</a>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
