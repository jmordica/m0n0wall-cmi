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
<p class="pgtitle"><?php echo $pagename; ?></p>

<?php if (isset($error)): ?>
<table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr>
  <td class="allbr">
    <span class="redcolor"><?php echo $error; ?></span>
  </td>
 </tr>
</table>
<?php endif; ?>

<br/><br/>

<?php if (isset($message)): ?>
<table width="100%" border="0" cellpadding=0" cellspacing="0">
 <tr>
  <td>
   <?php echo $message; ?>
  </td>
 </tr>
</table>
<?php endif; ?>

<br/><br/>

<?php if (isset($list) && is_array($list)): ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr id="frheader">
 <?php   
         foreach ($list[0] as $col): 
 ?>
  <td class="listhdrr"><center><?php echo $col; ?></center></td>
 <?php   
         endforeach; 
	 unset($list[0]); 
 ?>
 </tr>
<?php    
	foreach ($list as $entry) { 
	 echo "<tr>";
 	 $i=0;
	 foreach ($entry as $col) {
	
?>
  <td class="<?php echo ($i)?"listr":"listlr"; ?>"><center>
  <?php 
    if (is_array($col)) {
      if (isset($col["href"])) { 
        echo "<a href=\"".$col["href"]."\">";
      }
      if (isset($col["img"])) {
        echo "<img src=\"".$col["img"]."\" alt=\"".$col["label"]."\"/>";
      } else echo $col["label"];
      if (isset($col["href"])) { 
        echo "</a>";
      }
    } else {
     echo $col; 
    }
  ?>
  </center></td>
<?php    
	 $i++;
	 }
 	 echo "</tr>";
       }
?>
</table>
<?php endif; ?>

<br/><br/>

<?php if (isset($link)): ?>
<a href="<?php echo $link["href"]; ?>"><?php echo $link["label"]; ?></a><br/>
<?php endif; ?>
