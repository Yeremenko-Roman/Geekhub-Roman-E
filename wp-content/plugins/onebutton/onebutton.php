<?php
/*
Plugin Name: OneButton
Plugin URI: http://blog.sjinks.org.ua/php/wordpress/202-onebutton-better-version-of-odnaknopka/
Description: One Button for all Bookmark Services
Author: Vladimir Kolesnikov
Author URI: http://blog.sjinks.org.ua/
Version: 0.3b2
*/

    require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-config.php');

    class OneButton
    {
        protected $my_url;
        protected $template;
        protected $options;

        public function __construct()
        {
            add_action('init', array(&$this, 'init'));
        }

        protected function loadOptions()
        {
            $defaults = array(
                'only_single' => 1,
                'nofollow'    => 1,
                'newwindow'   => 0,
                'show_icons'  => 1,
                'services'    => array(
                    'blink'         => 1,
                    'delicious'     => 1,
                    'digg'          => 1,
                    'furl'          => 1,
                    'google'        => 1,
                    'simpy'         => 1,
                    'spurl'         => 1,
                    'ymyweb'        => 1,
                    'bobrdobr'      => 1,
                    'mrwong'        => 1,
                    'yabm'          => 1,
                    'text20'        => 1,
                    'news2'         => 1,
                    'addscoop'      => 1,
                    'ruspace'       => 1,
                    'rumarkz'       => 1,
                    'memory'        => 1,
                    'googlebm'      => 1,
                    'pisali'        => 1,
                    'smi2'          => 1,
                    'myplace'       => 1,
                    'bm100'         => 1,
                    'wow'           => 1,
                    'technorati'    => 1,
                    'rucity'        => 1,
                    'linkstore'     => 1,
                    'newsland'      => 1,
                    'lopas'         => 1,
                    'liua'          => 1,
                    'connotea'      => 1,
                    'bibsonomy'     => 1,
                    'trucking'      => 1,
                    'communizm'     => 1,
                    'uca'           => 1,
                ),
            );

//            delete_option('onebutton_settings');
            $options = get_option('onebutton_settings');
            if (false === $options || false === is_array($options)) {
                $this->options = $defaults;
                update_option('onebutton_settings', $this->options);
            }
            else {
                foreach ($defaults as $key => $value) {
                    if (false == isset($options[$key])) {
                        $options[$key] = $value;
                    }
                    elseif (true == is_array($defaults[$key])) {
                        foreach ($defaults[$key] as $k => $v) {
                            if (false == isset($options[$key][$k])) {
                                $options[$key][$k] = $v;
                            }
                        }
                    }
                }

                $this->options = $options;
            }
        }

		public function load_lang()
		{
		    static $done = false;

			if (false == $done) {
			    $done = true;
				load_plugin_textdomain('onebutton', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) . '/lang');

                ob_start();
                require(dirname(__FILE__) . '/onebutton_template.php');
                $this->template = ob_get_contents();
                ob_end_clean();

                $this->template = str_replace('{nofollow}', ((true == $this->options['nofollow'])  ? ' nofollow' : ''), $this->template);
                $this->template = str_replace('{target}',   ((true == $this->options['newwindow']) ? ' target="_blank"' : ''), $this->template);

                foreach ($this->options['services'] as $service => $val) {
                    if (false == $val) {
                        $service = preg_quote(strtoupper($service));
                        $this->template = preg_replace('/<!--\\s+' . $service . '\\s+-->(.*?)<!--\\s+\\/' . $service . '\\s+-->/sm', '', $this->template);
                    }
                }

                $this->template = preg_replace('/<!-- (.*?) -->/', '', $this->template);
			}
		}

        public function init()
        {
            add_action('wp_head', array(&$this, 'wp_head'));
            add_filter('the_content', array(&$this, 'the_content'), 60);

            add_action('admin_menu', array(&$this, 'admin_menu'));

            $this->my_url = get_option('siteurl') . '/wp-content/plugins/onebutton';

            $this->loadOptions();

            if (false == $this->options['only_single']) {
				add_action('loop_end', array(&$this, 'get_header'));
                wp_register_script('one_button', $this->my_url . '/onebutton.js');
                wp_enqueue_script('one_button');
            }
        }

        public function wp_head()
        {
            print <<< delimiter
<link rel="stylesheet" type="text/css" href="{$this->my_url}/onebutton.css" media="screen"/>
delimiter;
            if (0 == $this->options['show_icons']) {
                print <<< delimiter
<style type="text/css" media="screen">/*<![CDATA[*/
#sharepage { width: 438px; }
#sharepage ul li { background: none; padding: 0 !important; }
/*]]>*/
</style>
delimiter;
            }
        }

        public function get_header()
        {
            if (false == is_page() && false == is_single() && false == is_admin() && false == is_404() && false == is_trackback()) {
			    $this->load_lang();
                print $this->template;
            }
        }

        public function the_content($content)
        {
            $post_id = get_the_ID();
            if (get_post_meta($post_id, 'disable_onebutton', true)) {
                return $content;
            }

            $permalink   = apply_filters('the_permalink', get_permalink());
            $title       = the_title('', '', false);
            $description = get_post_meta($post_id, "description", true);
            $cats        = get_the_category();
            $tags        = get_the_terms(0, 'post_tag');

            $tax = array();
            if (false == empty($cats)) {
                foreach ($cats as $x) {
                    $tax[] = $x->name;
                }
            }

            if (false == empty($tags)) {
                foreach ($tags as $x) {
                    $tax[] = $x->name;
                }
            }

            $tax  = array_unique($tax);
            $tags = join(',', $tax);

            if (true == empty($description)) {
                $description = $title;
            }

			$this->load_lang();

            if (true == is_page() || true == is_single()) {
                $permalink   = urlencode($permalink);
                $title       = urlencode($title);
                $description = urlencode($description);
                $tags        = urlencode($tags);
                $template    = str_replace(array('{url}', '{title}', '{description}', '{tags}'), array($permalink, $title, $description, $tags), $this->template);

                return $content . '<div class="onebutton"><img class="atb" src="'. $this->my_url . '/bookmarks.png" alt="' . __('Add to Bookmarks', 'onebutton') . '" title="' . __('Add to Bookmarks', 'onebutton') . '"/><!--[if lte IE 6]><a href="#" class="obie"><table><tbody><tr><td><![endif]-->' . $template . '<!--[if lte IE 6]></td></tr></tbody></table></a><![endif]--></div>';
            }

            if (false == $this->options['only_single'] && false == is_admin() && false == is_404() && false == is_feed() && false == is_trackback()) {
                return $content . '<div class="onebutton" onmouseout="CloseOneButton()" onmouseover="OneButton(this, \'' . js_escape($permalink) . '\', \'' . js_escape($title) . '\', \'\', \'\')"><img class="atb2" src="'. $this->my_url . '/bookmarks.png" alt="' . __('Add to Bookmarks', 'onebutton') . '" title="' . __('Add to Bookmarks', 'onebutton') . '"/></div>';
            }

            return $content;
        }

        public function admin_menu()
        {
            if (true == function_exists('add_options_page')) {
                add_options_page(__('OneButton Options', 'onebutton'), 'OneButton', 8, basename(__FILE__), array(&$this, 'options_page'));
            }
        }

        public function options_page()
        {
            global $user_level;
            $message = '';

            $this->load_lang();

            get_currentuserinfo();
            if ($user_level < 8) {
                wp_die('<div class="wrap">' . __("You do not have enough privileges for modifying the options.", "onebutton") . '</div>');
            }

            if (true == isset($_POST['update_options'])) {
                foreach ($this->options as $key => $value) {
                    if (false == is_array($value)) {
                        $this->options[$key] = (isset($_POST['onebutton'][$key]) ? 1 : 0);
                    }
                    elseif (false == empty($value)) {
                        foreach ($value as $k => $v) {
                            $this->options[$key][$k] = (isset($_POST['onebutton'][$key][$k]) ? 1 : 0);
                        }
                    }
                }

                update_option('onebutton_settings', $this->options);
                $message = __("Settings have been updated", "onebutton");
                $this->loadOptions();
            }

            require(dirname(__FILE__) . '/options.php');
        }
    }

    $one_button = new OneButton();
?>