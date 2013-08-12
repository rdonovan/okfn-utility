<?php
/*
Plugin Name: OKFN Utility
Plugin URI: http://okfn.org
Description: Utility functions for OKFN Blogfarm
Version: 1.0
Author: Bobby Donovan
Author URI: http://bobbydonovan.com
Author Email: bobby@bobbydonovan.com
License:

  Copyright 2013 TODO (email@domain.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

$okfn_utility_inc_dir = ( WPMU_PLUGIN_DIR == dirname(__FILE__) ) ? WPMU_PLUGIN_DIR . '/okfn-utility' : dirname(__FILE__);
require_once( $okfn_utility_inc_dir . '/okfn-utility.php' );
require_once( $okfn_utility_inc_dir . '/okfn-utility-functions.php' );
$GLOBALS['okfn_utility'] = new OKFN_Utility();
$GLOBALS['okfn_utility']->init();
