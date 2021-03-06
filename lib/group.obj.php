<?php
 /**
  * Groups management
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage group
  * @category classes
  * @filesource
  */
 /*
    m0n0wall Central Management Interface
    Copyright (C) 2007  Gouverneur Thomas

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
  */

class Group extends MysqlObj
{
  public $id = -1;
  public $name = "";
  public $description = "";
  public $pages = "";

  public $idhost = -1;

  /* link */
  public $mono = NULL;

  public $_root = "group";
  public $_conf = array(
                                "name" => "var:name",
                                "description" => "varo:description",
                                "pages" => "ofct:getPages"
                        );

  function getPages()
  {
    $p = array();
    $pages = explode(';', $this->pages);
    $i = 0;
    foreach ($pages as $page) {
      $p[] = $page;
      $i++;
    }
    unset($p[$i-1]);
    return $p;
  }

  function existsInDb()
  {
    return $this->existsDb();
  }

  /* other */
  public function __construct($id=-1)
  {
    $this->id = $id;

    $this->_table = "group";

    $this->_my = array(
			"id" => SQL_INDEX,
			"name" => SQL_PROPE | SQL_EXIST | SQL_WHERE,
			"description" => SQL_PROPE,
			"pages" => SQL_PROPE,
			"idhost" => SQL_PROPE | SQL_EXIST | SQL_WHERE, 
			);

    $this->_myc = array( /* mysql => class */
                          "id" => "id",
	  		  "name" => "name",
			  "description" => "description",
			  "pages" => "pages",
			  "idhost" => "idhost"
		);

  }


}
