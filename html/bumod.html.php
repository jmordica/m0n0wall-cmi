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

 if (!(isset($_GET["action"]) && $_GET["action"] == 2) && !(isset($_POST["action"]) && $_POST["action"] == 3)) {
   $main = Main::getInstance();
   if (isset($_GET["bid"])) {
    $bu = new Buser($_GET["bid"]);
   } else if (isset($_POST["bid"])) {
    $bu = new Buser($_POST["bid"]);
   } else {
    echo "Error, no bid found in GET or POST<br/>\n"; die();
   }
   if (!$bu->fetchFromId()) {
    echo "Backup user not found, cannot continue..</br>\n";
    die();
   }
 }
?>

<p class="pgtitle">Backup user: Modification</p>

<?php if (!isset($_POST["action"]) || (isset($_GET["action"]) && $_GET["action"] == 2)) { /* modify existing or add new */ ?>
<?php if (!isset($bu)) $bu = new BUser(); ?>

 <form action="bumod.php" method="post">
 <input type="hidden" name="action" value="<?php if (!isset($_GET["action"])) echo 1; else echo 3; ?>">
 <input type="hidden" name="bid" value="<?php echo $bu->id; ?>">
 <table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr>
  <td width="22%" valign="top" class="vncellreq">Username</td>
  <td width="78%" class="vtable"><input name="username" type="text" class="formfld" id="username" size="40" value="<?php echo $bu->login; ?>">
  <br/> <span class="vexpl">Login of the backup user to get/put m0n0wall's configuration<br/></span></td>
 </tr>
 <tr>
  <td width="22%" valign="top" class="vncellreq">Password</td>
  <td width="78%" class="vtable"><input name="password" type="password" class="formfld" id="password" size="40" value="">
  <br/> <span class="vexpl">Password of the backup user (let empty not to change it)<br/></span></td>
 </tr>
 <tr>
  <td width="22%" valign="top" class="vncellreq">Description</td>
  <td width="78%" class="vtable"><textarea name="description" class="formfld" id="description" size="40"><?php echo $bu->description; ?></textarea>
  <br/> <span class="vexpl">Description for the backup user<br/></span></td>
 </tr>

 <tr> 
  <td width="22%" valign="top">&nbsp;</td>
   <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="Save"> 
   </td>
 </tr>
 </table>
 </form>
<?php } else if ($_POST["action"] == 1) {

  if ($_POST["action"] == 1) /* updates fields in DB */
  {
    $mod = 0;
    if ($bu->login != mysql_escape_string($_POST["username"])) /* login updated */
    {
      $bu->login = mysql_escape_string($_POST["username"]);
      $mod = 1;
    }

    if (!empty($_POST["password"])) /* password to update */
    {
      $bu->password = mysql_escape_string($_POST["password"]);
      $mod = 1;
    }
 
    if (mysql_escape_string($_POST["description"]) != $bu->description) /* description to update */
    {
      $bu->description = mysql_escape_string($_POST["description"]);
      $mod = 1;
    }

    if ($mod) { 
      $bu->update();
      echo "Modification of backup user saved.<br/>\n";
    } else {
      echo "No Modification to perform.<br/>\n";
    }

  } else { /* unmatched action */
    echo "Error, unmatched action in form<br/>\n";
  }

 } else if ($_POST["action"] == 3) { /* add new into db */

  $bu = new Buser();

  if (empty($_POST["username"]))
  { echo "Empty username not permitted<br/>\n"; die(); }
  $bu->login = mysql_escape_string($_POST["username"]);

  if (empty($_POST["password"]))
  { echo "Empty password not permitted<br/>\n"; die(); }
  $bu->password = mysql_escape_string($_POST["password"]);

  $bu->description = mysql_escape_string($_POST["description"]);

  if ($bu->existsDb())
  { echo "User already exist in the db..<br/>\n"; die(); }

  $bu->insert();
  echo "User added into the database<br/>\n";

 } else { /* POST["action"] is set */

 } 
?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
<br/><a href="busers.php">Return to Backup Users list</a><br/>
