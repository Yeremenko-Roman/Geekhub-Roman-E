<?php

function ru_extend_menu() { ?>
	
<style type="text/css">
	#adminmenu {
		width: 185px; /* default 145px + 10px */
		margin-left: -200px; /* default 160px + 10px */
	}
	#wpbody {
		margin-left: 215px; /* default 175px + 10px */
	}
	* html #adminmenu { /* for IE6 only */
		margin-left: -115px; /* default 80px + 5px */
	}
	#minor-publishing-actions {
	    padding: 3px;
		text-align: right;
		}

	#dashboard_quick_press .input-text-wrap,
	#dashboard_quick_press .textarea-wrap {
	    margin: 0 0 1em 6em;
        }
	
	.inline-edit-row fieldset label span.input-text-wrap {
	    display: block;
	    margin-left: 6em;
	}
	.inline-edit-row fieldset .inline-edit-date {
        float: left;
	margin-left: 1em;
        }
	
	.inline-edit-row fieldset label input.inline-edit-menu-order-input {
        width: 3em;
	margin-left: -0.85em;    
	}

.select-action
{
width: 180px !important;
}
.actions select
{
width: 180px !important;
}
</style>
<?php
}
function filter_timeout_for_updates($r)
{
        if (30 == $r['timeout'])
                $r['timeout'] = 300;
        return ($r);
}
function change_update_url($options) {
if (isset($options->updates) && is_array($options->updates))
foreach ( $options->updates as $key => $value ) {
if ($value != '')
{
$options->updates[$key] = (object)
str_replace('http://ru.wordpress.org/',
'http://lecactus.ru/download/', (array) $value); 
}
}
        return $options;
}
add_filter('pre_update_option__transient_update_core', 'change_update_url');
add_filter('pre_update_option_update_core', 'change_update_url');
add_filter('http_request_args','filter_timeout_for_updates');
add_action('admin_head', 'ru_extend_menu');
?>