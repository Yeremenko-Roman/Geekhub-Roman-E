<?php
/*
Plugin Name: Semisecure Login Reimagined
Plugin URI: http://moggy.laceous.com/2009/09/05/semisecure-login-reimagined-v3/
Description: This plugin increases the security of the login process by encrypting the password on the client-side. JavaScript is required to enable encryption. It is most useful for situations where SSL is not available, but the administrator wishes to have some additional security measures in place without sacrificing convenience.
Version: 3.0.8.1
Author: moggy
Author URI: http://moggy.laceous.com/
*/

/*
    Copyright 2008-2009

    Based on Semisecure Login (http://wordpress.org/extend/plugins/semisecure-login/)
    and Semisecure Login for WordPress 2.5 (http://wordpress.org/extend/plugins/semisecure-login-for-25/)

    Thanks to:
      - BigIntegers and RSA in JavaScript (http://www-cs-students.stanford.edu/~tjw/jsbn/)
      - crypto-js (http://code.google.com/p/crypto-js/)
      - famfamfam (http://www.famfamfam.com/lab/icons/silk/)

    This plugin requires at least WP 2.7 and PHP 4.3

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once(dirname(__FILE__).'/classes/SemisecureLoginReimagined.php');
new SemisecureLoginReimagined; // create an instance of the main class
?>