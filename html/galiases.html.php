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
     
 $main->fetchGAliases();
 $main->fetchGAliasesDetails();

?>
<p class="pgtitle">Global Aliases: List</p>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr id="frheader">
  <td class="listhdrr"><center>Name</center></td>
  <td class="listhdrr"><center>Address</center></td>
  <td class="listhdrr"><center>Description</center></td>
  <td class="listhdrr"><center>Edit</center></td>
  <td class="listhdrr"><center>Del</center></td>
 </tr>
<?php
  foreach($main->galiases as $alias) {
    ?>  <tr>
    <td class="listlr"><center><?php echo $alias->name; ?></center></td>
    <td class="listr"><center><?php echo $alias->address; ?></center></td>
    <td class="listr"><center><?php echo empty($alias->description)?"-":$alias->description; ?></center></td>
    <td class="listr"><center><a href="galiasmod.php?&gaid=<?php echo $alias->id; ?>">X</a></center></td>
    <td class="listr"><center><a href="galiasrm.php?gaid=<?php echo $alias->id; ?>">X</a></center></td>
   </tr>
   <?php
  }
?></table><br/><a href="galiasmod.php">Add new global alias</a><?php

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
