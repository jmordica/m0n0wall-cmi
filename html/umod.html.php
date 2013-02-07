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


 if (isset($_GET["uid"])) $uid = $_GET["uid"];
 if (isset($_POST["uid"])) $uid = $_POST["uid"];

 if (isset($_GET["mid"])) $mid = $_GET["mid"];
 if (isset($_POST["mid"])) $mid = $_POST["mid"];

 if (isset($_GET["action"])) $action = $_GET["action"];
 if (isset($_POST["action"])) $action = $_POST["action"];
 if (!isset($action)) $action = 1;


 if (isset($mid) && $mid) {
  $mono = new Monowall($mid);
  $mono->fetchFromId();
  $mono->fetchGroups();
  if (isset($uid) && $uid) {
   $u = new User($uid);
   $u->fetchFromId();
  }
 } else die("No monowall selected");

 
?>
<p class="pgtitle">Users: Edit</p>

<?php if ($action == 1) { ?>
<form action="umod.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
  <tr> 
   <td width="22%" valign="top" class="vncellreq">Username</td>
   <td width="78%" class="vtable"> 
	<input name="username" type="text" class="formfld" id="username" size="20" value="<?=$u->name;?>"> 
   </td>
  </tr>
  <tr> 
   <td width="22%" valign="top" class="vncellreq">Password</td>
   <td width="78%" class="vtable"> 
   <input name="password" type="password" class="formfld" id="password" size="20" value=""> <br>
   <input name="password2" type="password" class="formfld" id="password2" size="20" value="">
&nbsp;(confirmation)					</td>
  </tr>
  <tr> 
   <td width="22%" valign="top" class="vncell">Full name</td>
   <td width="78%" class="vtable"> 
   <input name="fullname" type="text" class="formfld" id="fullname" size="20" value="<?=$u->fullname;?>">
    <br>
    User's full name, for your own information only</td>
  </tr>
  <tr> 
   <td width="22%" valign="top" class="vncell">Group Name</td>
   <td width="78%" class="vtable">
    <select name="groupname" class="formfld" id="groupname">
<?php foreach($mono->group as $grp) { ?>
     <option value="<?=$grp->id;?>"<?php if ($grp->name == $u->groupname) echo "selected"; ?>><?=$grp->name;?></option>
<?php } ?>
    </select>                   
      <br>
      The admin group to which this user is assigned.</td>
  </tr>                
  <tr> 
   <td width="22%" valign="top">&nbsp;</td>
   <td width="78%"> 
<?php if (isset($uid) && $uid && isset($u) && $u) { ?>
   <input name="uid" type="hidden" value="<?=$u->id;?>">
   <input name="action" type="hidden" value="3">
<?php } else { ?>
   <input name="action" type="hidden" value="2">
<?php } ?>
   <input name="save" type="submit" class="formbtn" value="Save"> 
   <input name="mid" type="hidden" value="<?=$mono->id;?>">
   </td>
  </tr>
 </table>
</form>
<?php } else if ($action == 2) /* add */ { 


$u = new User();
if (isset($_POST["username"]) && !empty($_POST["username"]) &&
    isset($_POST["password"]) && !empty($_POST["password"]) &&
    isset($_POST["password2"]) && !empty($_POST["password2"]) &&
    isset($_POST["fullname"]) && !empty($_POST["fullname"]) &&
    isset($_POST["groupname"]) && !empty($_POST["groupname"])) {

  if ($_POST["password"] != $_POST["password2"]) die("Password and confirmation does not match...");

  $u->name = mysql_escape_string($_POST["username"]);
  $u->password = crypt(mysql_escape_string($_POST["password"]));
  $u->fullname = mysql_escape_string($_POST["fullname"]);
  $grp = new Group(mysql_escape_string($_POST["groupname"]));
  $grp->fetchFromId();
  $u->groupname = $grp->name;
  $u->idhost = $mono->id;
  $u->mono = $mono;

  if (!$u->existsDb()) {
    $u->insert();
    $mono->updateChanged();
  }
  else die("Object alredy in database");

  echo "User inserted<br/>";
}
 else {
  die("Missing Field..");
}


} else if ($action == 3) /* mod */ {

if (!(isset($u) && $u)) die("No such user found for modification");

if (isset($_POST["username"]) && !empty($_POST["username"]) &&
    isset($_POST["fullname"]) && !empty($_POST["fullname"]) &&
    isset($_POST["groupname"]) && !empty($_POST["groupname"])) {
 
  $mod = 0;
  if (mysql_escape_string($_POST["username"]) != $u->name) {
   $mod = 1;
   $u->name = mysql_escape_string($_POST["username"]);
  }

  if (isset($_POST["password"]) && isset($_POST["password2"]) && $_POST["password"] == $_POST["password2"] && mysql_escape_string($_POST["password"]) != $u->password) {
    $u->password = crypt(mysql_escape_string($_POST["password"]));
    $mod = 1;
  }

  if (mysql_escape_string($_POST["fullname"]) != $u->fullname) {
    $mod = 1;
    $u->fullname = mysql_escape_string($_POST["fullname"]);
  }

  $g = new Group(mysql_escape_string($_POST["groupname"]));
  $g->fetchFromId();
  if ($g && $g->name != $u->groupname) {
   $mod = 1;
   $u->groupname = $g->name;
  }

  if ($mod) {
    $u->update();
    $mono->updateChanged();
    echo "User Updated<br/>";
  }
}

} ?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
