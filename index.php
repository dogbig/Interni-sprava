<?php

//    ******************************
//    Interní správa - Bc. projekt
//    Copyright (C) 2011-2012  Michal Charvát & FM TUL
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.

//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.

//    You should have received a copy of the GNU General Public License
//    along with this program.  If not, see http://www.gnu.org/licenses/
//    ******************************

// uncomment this line if you must temporarily take down your site for maintenance
// require '.maintenance.php';

$params = array();

// absolute filesystem path to this web root
$params['wwwDir'] = __DIR__;

// absolute filesystem path to the application root
$params['appDir'] = realpath(__DIR__ . '/app');

// load bootstrap file
require $params['appDir'] . '/bootstrap.php';

?>
