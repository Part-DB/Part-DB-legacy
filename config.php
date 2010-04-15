<?PHP
/*
	part-db version 0.1
	Copyright (C) 2005 Christoph Lechner
	http://www.cl-projects.de/

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA

	$Id$

	ChangeLog
	02/12/2009
		Split of from lib.php
*/

	function partdb_init()
	{
		/* Enter your MySQL username and password here. For
		   optimal protection you should adjust the UNIX perms
		   of this file in a way only the PHP interpreter can read
		   it. But this does not protect your data from malcious
		   users. Each user should run his PHP scripts with his
		   own user id. */
		$link = mysql_connect ("localhost", "part-db", "PARTdb");
		mysql_select_db ("part-db");
	}

?>
