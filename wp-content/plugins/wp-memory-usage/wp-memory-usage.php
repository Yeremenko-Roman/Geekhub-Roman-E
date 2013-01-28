<?php
/*
Plugin Name: WP-Memory-Usage
Plugin URI: http://alexrabe.boelinger.com/
Description: Show up the memory limit and current memory usage in the dashboard and admin footer
Author: Alex Rabe
Version: 1.1.0

Author URI: http://alexrabe.boelinger.com/

Copyright 2009 by Alex Rabe 

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

if ( is_admin() ) {	
	
	class wp_memory_usage {
		
		var $memory = false;
		
		function wp_memory_usage() {
			return $this->__construct();
		}

		function __construct() {

			add_action( 'wp_dashboard_setup', array (&$this, 'add_dashboard') );
			add_filter( 'admin_footer_text', array (&$this, 'add_footer') );

			$this->memory = array();					
		}
		
		function check_memory_usage() {
			
			$this->memory['usage'] = function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 2) : 0;
			$this->memory['limit'] = (int) ini_get('memory_limit') ;
			
			if ( !empty($this->memory['usage']) && !empty($this->memory['limit']) ) {
				$this->memory['percent'] = round ($this->memory['usage'] / $this->memory['limit'] * 100, 0);
				$this->memory['color'] = '#21759B';
				if ($this->memory['percent'] > 80) $this->memory['color'] = '#E66F00';
				if ($this->memory['percent'] > 95) $this->memory['color'] = 'red';
			}		
		}
		
		function dashboard_output() {
			
			$this->check_memory_usage();
			
			$this->memory['limit'] = empty($this->memory['limit']) ? __('N/A') : $this->memory['limit'] . __(' MByte');
			$this->memory['usage'] = empty($this->memory['usage']) ? __('N/A') : $this->memory['usage'] . __(' MByte');
			
			?>
				<ul>	
					<li><strong><?php _e('PHP Version'); ?></strong> : <span><?php echo PHP_VERSION; ?>&nbsp;/&nbsp;<?php echo (PHP_INT_SIZE * 8) . __('Bit OS'); ?></span></li>
					<li><strong><?php _e('Memory limit'); ?></strong> : <span><?php echo $this->memory['limit']; ?></span></li>
					<li><strong><?php _e('Memory usage'); ?></strong> : <span><?php echo $this->memory['usage']; ?></span></li>
				</ul>
				<?php if (!empty($this->memory['percent'])) : ?>
				<div class="progressbar">
					<div class="widget" style="height:2em; border:1px solid #DDDDDD; background-color:#F9F9F9;">
						<div class="widget" style="width: <?php echo $this->memory['percent']; ?>%;height:100%;background-color:<?php echo $this->memory['color']; ?> ;border-width:0px;text-shadow:0 1px 0 #000000;color:#FFFFFF;text-align:right;font-weight:bold;"><div style="padding:6px"><?php echo $this->memory['percent']; ?>%</div></div>
					</div>
				</div>
				<?php endif; ?>
			<?php
		}
		 
		function add_dashboard() {
			wp_add_dashboard_widget( 'wp_memory_dashboard', 'Memory Overview', array (&$this, 'dashboard_output') );
		}
		
		function add_footer($content) {
			
			$this->check_memory_usage();
			
			$content .= ' | Memory : ' . $this->memory['usage'] . ' of ' . $this->memory['limit'] . ' MByte';
			
			return $content;
		}

	}

	// Start this plugin once all other plugins are fully loaded
	add_action( 'plugins_loaded', create_function('', '$memory = new wp_memory_usage();') );
}