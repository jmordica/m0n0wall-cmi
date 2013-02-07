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
?>
<p class="pgtitle">General: Statistics</p>

<ul>
<li>Number of monowall devices in the database: <?php echo Mysql::getInstance()->count("hosts"); ?></li>
<li>Number of interfaces in the databases: <?php echo Mysql::getInstance()->count("interfaces"); ?></li>
<li>Number of firewall rules in the databases: <?php echo Mysql::getInstance()->count("rules"); ?></li>
<li>Number of NAT rules in the databases: <?php echo Mysql::getInstance()->count("nat-srv")+Mysql::getInstance()->count("nat-rules")+Mysql::getInstance()->count("nat-advout")+Mysql::getInstance()->count("nat-one2one"); ?></li>
<li>Number of Aliases in the databases: <?php echo Mysql::getInstance()->count("alias"); ?></li>
<li>Number of Global Aliases in the databases: <?php echo Mysql::getInstance()->count("galias"); ?></li>
<li>Number of Users in the databases: <?php echo Mysql::getInstance()->count("user"); ?></li>
<li>Number of Groups in the databases: <?php echo Mysql::getInstance()->count("group"); ?></li>
<li>Number of ProxyARP in the databases: <?php echo Mysql::getInstance()->count("proxyarp"); ?></li>
<li>Number of Static Routes in the databases: <?php echo Mysql::getInstance()->count("staticroutes"); ?></li>
<li>Number of VLANs in the databases: <?php echo Mysql::getInstance()->count("vlans"); ?></li>
</ul>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
