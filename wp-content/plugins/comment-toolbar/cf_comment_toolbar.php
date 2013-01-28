<?PHP

/* 
  Plugin Name: Comment Toolbar
  Plugin URI: http://www.cristianofino.net/post/Comment-toolbar-plugin-per-Wordpress.aspx
  Description: Adds Comments Navigation, Quote and Reply buttons (or links) at the beginning or end of each comment. You can customize every aspect of the toolbar (smooth scrolling powered now!).
  Version: 1.4.00
  Author: Cristiano Fino
  Author URI: http://www.cristianofino.net/
*/

/* 
  Copyright 2008 Cristiano Fino (email: cristiano.fino@bbs.olografix.org)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/ 

if (function_exists('cf_comment_toolbar')) 
{
	add_filter('comment_text', 'cf_comment_toolbar', 99);
	
	/* Adding parameters */
	add_option('cf_comment_toolbar_lbl_reply', __('Reply','cf_comment_toolbar'), 'label of Reply button/link');
	add_option('cf_comment_toolbar_lbl_quote', __('Quote','cf_comment_toolbar'), 'label of Quote button/link');
	add_option('cf_comment_toolbar_lbl_wrote', __('wrote','cf_comment_toolbar'), 'text of Wrote message');

	add_option('cf_comment_toolbar_position', 'T', 'toolbar position: T - Top, B - Bottom');
	add_option('cf_comment_toolbar_align', 'right', 'toolbar alignment: left, right, center, css');
	add_option('cf_comment_toolbar_style','B','toolbar style: B - Buttons, L - Links, I - Images');
	add_option('cf_comment_toolbar_separator','&nbsp;|&nbsp;','separator characters');
	
	add_option('cf_comment_toolbar_navigation','1','display or not comment navigation link: 1 - Display, 0 - Hide');
	add_option('cf_comment_toolbar_anchor_prefix','comment-','text before the comment on anchor link');	
	add_option('cf_comment_toolbar_enance_text','1','enclose commentator name in tag <b>...</b>');	
	add_option('cf_comment_toolbar_scrolling','0','smooth scroling: 1 - Enable, 0 - Disable');	
	add_option('cf_comment_toolbar_author_link','0','anchor to comment author: 1 - Enable, 0 - Disable');

	add_option('cf_comment_toolbar_textarea_id','comment','comment textarea ID');		

	/* Adding menu and parameters page */
	add_action('admin_menu', 'cf_comment_toolbar_admin_menu');	
	
	/* Adding hook to header */
	add_action('wp_head', 'cf_comment_toolbar_js', 99);

	/* Loading text domain */
	load_plugin_textdomain('cf_comment_toolbar', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/languages', dirname(plugin_basename(__FILE__)).'/languages');	
}

/* Adding parameters page */

function cf_comment_toolbar_admin_menu()
{
	add_submenu_page('plugins.php', 'Comment Toolbar Plugin Options', 'Comment Toolbar', 5, basename(__FILE__),'cf_comment_toolbar_options_page'); 
}

/* Options page */

function cf_comment_toolbar_options_page() {

    $hidden_field_name = 'mt_submit_hidden';
    
    $opt_name_lbl_reply = 'cf_comment_toolbar_lbl_reply';
    $opt_name_lbl_quote = 'cf_comment_toolbar_lbl_quote';
    $opt_name_lbl_wrote = 'cf_comment_toolbar_lbl_wrote';
    $opt_name_position = 'cf_comment_toolbar_position';
    $opt_name_style = 'cf_comment_toolbar_style';
    $opt_name_align = 'cf_comment_toolbar_align';
    $opt_name_separator = 'cf_comment_toolbar_separator';
    $opt_name_navigation = 'cf_comment_toolbar_navigation';
    $opt_name_anchor_prefix = 'cf_comment_toolbar_anchor_prefix';
    $opt_name_enance_text = 'cf_comment_toolbar_enance_text';  
    $opt_name_scrolling = 'cf_comment_toolbar_scrolling';    
    $opt_name_author_link = 'cf_comment_toolbar_author_link';

    $data_field_name_lbl_reply = 'cf_comment_toolbar_lbl_reply';
    $data_field_name_lbl_quote = 'cf_comment_toolbar_lbl_quote';
    $data_field_name_lbl_wrote = 'cf_comment_toolbar_lbl_wrote';
    $data_field_name_position = 'cf_comment_toolbar_position';
    $data_field_name_style = 'cf_comment_toolbar_style';
    $data_field_name_align = 'cf_comment_toolbar_align';
    $data_field_name_separator = 'cf_comment_toolbar_separator';
    $data_field_name_navigation = 'cf_comment_toolbar_navigation';
    $data_field_name_anchor_prefix = 'cf_comment_toolbar_anchor_prefix';    
    $data_field_name_enance_text = 'cf_comment_toolbar_enance_text';    
    $data_field_name_scrolling = 'cf_comment_toolbar_scrolling';    
    $data_field_name_author_link = 'cf_comment_toolbar_author_link';
    
    // Read in existing option value from database
    $opt_val_lbl_reply = get_option($opt_name_lbl_reply);
    $opt_val_lbl_quote = get_option($opt_name_lbl_quote);
    $opt_val_lbl_wrote = get_option($opt_name_lbl_wrote);
    $opt_val_position = get_option($opt_name_position);
    $opt_val_style = get_option($opt_name_style);
    $opt_val_align = get_option($opt_name_align);
    $opt_val_separator = get_option($opt_name_separator);
    $opt_val_navigation = get_option($opt_name_navigation);
    $opt_val_anchor_prefix = get_option($opt_name_anchor_prefix);
    $opt_val_enance_text = get_option($opt_name_enance_text);
    $opt_val_scrolling = get_option($opt_name_scrolling);
    $opt_val_author_link = get_option($opt_name_author_link);
    
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        $opt_val_lbl_reply = $_POST[$data_field_name_lbl_reply];
        $opt_val_lbl_quote = $_POST[$data_field_name_lbl_quote];
        $opt_val_lbl_wrote = $_POST[$data_field_name_lbl_wrote];
        $opt_val_position = $_POST[$data_field_name_position];
        $opt_val_style = $_POST[$data_field_name_style];
        $opt_val_align = $_POST[$data_field_name_align];
        $opt_val_separator = $_POST[$data_field_name_separator];
        $opt_val_navigation = $_POST[$data_field_name_navigation];
        $opt_val_anchor_prefix = $_POST[$data_field_name_anchor_prefix];
        $opt_val_enance_text = $_POST[$data_field_name_enance_text];
        $opt_val_scrolling = $_POST[$data_field_name_scrolling];
        $opt_val_author_link = $_POST[$data_field_name_author_link];
                
        update_option($opt_name_lbl_reply, $opt_val_lbl_reply);
        update_option($opt_name_lbl_quote, $opt_val_lbl_quote);
        update_option($opt_name_lbl_wrote, $opt_val_lbl_wrote);
        update_option($opt_name_style, $opt_val_style);
        update_option($opt_name_align, $opt_val_align);
        update_option($opt_name_position, $opt_val_position);		
        update_option($opt_name_separator, $opt_val_separator);		
        update_option($opt_name_navigation, $opt_val_navigation);		
        update_option($opt_name_anchor_prefix, $opt_val_anchor_prefix);	
        update_option($opt_name_enance_text, $opt_val_enance_text);		        	
        update_option($opt_name_scrolling, $opt_val_scrolling);		        	
        update_option($opt_name_author_link, $opt_val_author_link);
               
?>
	<div id="message" class="updated fade"><p><strong><?php _e('Options saved.', 'cf_comment_toolbar' ); ?></strong></p></div>

<?php
    }
    echo '<div class="wrap">';
    echo "<h2>" . __( 'Comment Toolbar Plugin Options', 'cf_comment_toolbar' ) . "</h2>";
    ?>

	<form name="form_comment_toolbar_Options" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">	
	<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="<?php echo $data_field_name_lbl_reply; ?>"><?php _e("Label of 'Reply' button (or link):", 'cf_comment_toolbar' ); ?></label>
		</th>
		<td>
			<input type="text" name="<?php echo $data_field_name_lbl_reply; ?>" value="<?php echo $opt_val_lbl_reply; ?>" size="20"><br />
			<span class="setting-description"><?php _e("(to not display, leave blank)", 'cf_comment_toolbar' ); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">		
			<label for="<?php echo $data_field_name_lbl_quote; ?>"><?php _e("Label of 'Quote' button (or link):", 'cf_comment_toolbar' ); ?></label>
		</th>
		<td>
			<input type="text" name="<?php echo $data_field_name_lbl_quote; ?>" value="<?php echo $opt_val_lbl_quote; ?>" size="20"><br />
			<span class="setting-description"><?php _e("(to not display, leave blank)", 'cf_comment_toolbar' ); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">		
			<label for="<?php echo $data_field_name_lbl_wrote; ?>"><?php _e("Text message of 'Quote' action:", 'cf_comment_toolbar' ); ?></label>
		</th>
		<td>
			<input type="text" name="<?php echo $data_field_name_lbl_wrote; ?>" value="<?php echo $opt_val_lbl_wrote; ?>" size="30">
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="<?php echo $data_field_name_separator; ?>"><?php _e("Characters of separation between the buttons (or links):", 'cf_comment_toolbar' ); ?></label>
		</th>
		<td>
			<input type="text" name="<?php echo $data_field_name_separator; ?>" value="<?php echo $opt_val_separator; ?>" size="10"><br />
			<span class="setting-description"><?php _e("(you can insert HTML code also)", 'cf_comment_toolbar' ); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="<?php echo $data_field_name_enance_text; ?>"><?php _e("Enclose commentator's name in html tag &lt;b&gt;...&lt;/b&gt; :", 'cf_comment_toolbar' ); ?></label>
		</th>
		<td>
			<select name="<?php echo $data_field_name_enance_text; ?>">
				<option value="0" <?php if($opt_val_enance_text == "0") echo 'selected' ?> ><?php _e("No", 'cf_comment_toolbar' ); ?></option> 
				<option value="1" <?php if($opt_val_enance_text == "1") echo 'selected' ?> ><?php _e("Yes", 'cf_comment_toolbar' ); ?> </option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="<?php echo $data_field_name_author_link; ?>"><?php _e("Add a link to his commentary on the name of the commentator :", 'cf_comment_toolbar' ); ?></label>
		</th>
		<td>
			<select name="<?php echo $data_field_name_author_link; ?>">
				<option value="0" <?php if($opt_val_author_link == "0") echo 'selected' ?> ><?php _e("No", 'cf_comment_toolbar' ); ?></option> 
				<option value="1" <?php if($opt_val_author_link == "1") echo 'selected' ?> ><?php _e("Yes", 'cf_comment_toolbar' ); ?> </option>
			</select>
		</td>
	</tr>	
	<tr valign="top">
		<th scope="row">
			<label for="<?php echo $data_field_name_anchor_prefix; ?>"><?php _e("Text to insert in anchor link, before comment ID :", 'cf_comment_toolbar' ); ?></label>
		</th>
		<td>
			<input type="text" name="<?php echo $data_field_name_anchor_prefix; ?>" value="<?php echo $opt_val_anchor_prefix; ?>" size="20"><br />
			<span class="setting-description"><?php _e("(Insert <b>co_</b> if you do not know the format's links to comments of your theme)", 'cf_comment_toolbar' ); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="<?php echo $data_field_name_style; ?>"><?php _e("Toolbar style:", 'cf_comment_toolbar' ); ?></label>
		</th>
		<td>
			<select name="<?php echo $data_field_name_style; ?>">
				<option value="B" <?php if($opt_val_style == "B") echo 'selected' ?> ><?php _e("Buttons", 'cf_comment_toolbar' ); ?></option> 
				<option value="L" <?php if($opt_val_style == "L") echo 'selected' ?> ><?php _e("Links", 'cf_comment_toolbar' ); ?> </option>
				<option value="I" <?php if($opt_val_style == "I") echo 'selected' ?> ><?php _e("Images", 'cf_comment_toolbar' ); ?> </option>
			</select><br />
			<span class="setting-description"><?php _e("To customize the graphic appearance of image buttons, replace files 'first.png','previous.png', 'next.png', 'last.png', 'reply.png','quote.png', in the plugin's <b>images</b> folder using the same names for files", 'cf_comment_toolbar'); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="<?php echo $data_field_name_position; ?>"><?php _e("Toolbar position:", 'cf_comment_toolbar' ); ?></label>
		</th>
		<td>
			<select name="<?php echo $data_field_name_position; ?>">
				<option value="T" <?php if($opt_val_position == "T") echo 'selected' ?> ><?php _e("Top of comment", 'cf_comment_toolbar' ); ?></option> 
				<option value="B" <?php if($opt_val_position == "B") echo 'selected' ?> ><?php _e("Bottom of comment", 'cf_comment_toolbar' ); ?></option> 
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="<?php echo $data_field_name_align; ?>"><?php _e("Toolbar alignment:", 'cf_comment_toolbar' ); ?></label>
		</th>
		<td>
			<select name="<?php echo $data_field_name_align; ?>">
				<option value="left" <?php if($opt_val_align == "left") echo 'selected' ?> ><?php _e("Left", 'cf_comment_toolbar' ); ?></option> 
				<option value="right" <?php if($opt_val_align == "right") echo 'selected' ?> ><?php _e("Right", 'cf_comment_toolbar' ); ?></option> 
				<option value="center" <?php if($opt_val_align == "center") echo 'selected' ?> ><?php _e("Center", 'cf_comment_toolbar' ); ?></option> 
				<option value="css" <?php if($opt_val_align == "css") echo 'selected' ?> ><?php _e("CSS style settings", 'cf_comment_toolbar' ); ?></option> 
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="<?php echo $data_field_name_navigation; ?>"><?php _e("Show buttons to navigate on your comments :", 'cf_comment_toolbar' ); ?></label>
		</th>
		<td>
			<select name="<?php echo $data_field_name_navigation; ?>">
				<option value="0" <?php if($opt_val_navigation == "0") echo 'selected' ?> ><?php _e("No", 'cf_comment_toolbar' ); ?></option> 
				<option value="1" <?php if($opt_val_navigation == "1") echo 'selected' ?> ><?php _e("Yes", 'cf_comment_toolbar' ); ?></option>
			</select><br />
			<span class="setting-description"><?php _e("This feature is automatically disabled when using the paging of the comments", 'cf_comment_toolbar'); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="<?php echo $data_field_name_scrolling; ?>"><?php _e("Enable smooth scrolling :", 'cf_comment_toolbar' ); ?></label>
		</th>
		<td>
			<select name="<?php echo $data_field_name_scrolling; ?>">
				<option value="0" <?php if($opt_val_scrolling == "0") echo 'selected' ?> ><?php _e("No", 'cf_comment_toolbar' ); ?></option> 
				<option value="1" <?php if($opt_val_scrolling == "1") echo 'selected' ?> ><?php _e("Yes", 'cf_comment_toolbar' ); ?> </option>
			</select><br />
			<span class="setting-description"><?php _e("This feature is automatically disabled when using the paging of the comments", 'cf_comment_toolbar'); ?></span>
		</td>
	</tr>
	</table>
	<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php _e('Update Options', 'cf_comment_toolbar' ) ?>" />
	</p>
	</form>
	</div>

<?php
}

/* Core function of Comment Toolbar */

function cf_comment_toolbar_js()
{
	if (is_single() && comments_open())
	{
		$msg_wrote = get_option('cf_comment_toolbar_lbl_wrote');
		$enance = get_option('cf_comment_toolbar_enance_text');
		$textarea_id = get_option('cf_comment_toolbar_textarea_id');
		$anchor_prefix = (string)get_option('cf_comment_toolbar_anchor_prefix');
		$scrolling = (string)get_option('cf_comment_toolbar_scrolling');
		$author_link = (string)get_option('cf_comment_toolbar_author_link');

		if ($enance == '1') {
			$en_start = '<b>'; $en_end = '</b>';
		} else {
			$en_start = ''; $en_end = '';
		  }
?>
<script type="text/javascript">
<!--//
//
// Wordpress Comment Toolbar plugin: Javascript functions
//
// Plugin release:   1.3.1 beta
// Author:           Cristiano Fino
// WebSite:          http://www.cristianofino.net
//
function CF_Quote(id, oauthor) {
  var otext = document.getElementById('co_' + id);
  var otextCommentArea = document.getElementById("comment");
<?PHP
		/* Enable or disable link to comment author */
		if ($author_link == "1")
		{
?>
  oauthor = '<a href="#<?PHP echo($anchor_prefix) ?>' + id + '" title="<?PHP echo _e("Go to comment of this author", 'cf_comment_toolbar' ); ?>">' + oauthor + '</a>';
<?PHP } ?>
  if (window.getSelection)
	 var sel = window.getSelection();
  else if (document.getSelection)
	 var sel = document.getSelection();
  else if (document.selection) {
	 var sel = document.selection.createRange().text; }        
  if (otext.innerText){
	  if (sel != "") otextCommentArea.value += "<?PHP echo $en_start; ?>" + oauthor + "<?PHP echo $en_end.' '.$msg_wrote; ?>:\n<blockquote>" + sel + "</blockquote>\n"; 
		else otextCommentArea.value += "<?PHP echo $en_start; ?>" + oauthor + "<?PHP echo $en_end.' '.$msg_wrote; ?>:\n<blockquote>" + otext.innerText + "</blockquote>\n";
  }
  else { 
	  if (sel != "") otextCommentArea.value += "<?PHP echo $en_start; ?>" + oauthor + "<?PHP echo $en_end.' '.$msg_wrote; ?>:\n<blockquote>" + sel + "</blockquote>\n"; 
		else otextCommentArea.value += "<?PHP echo $en_start; ?>" + oauthor + "<?PHP echo $en_end.' '.$msg_wrote; ?>:\n<blockquote>" + otext.textContent + "</blockquote>\n";
  }
  otextCommentArea.focus();
}
function CF_Reply(id, oauthor) {
  var otextCommentArea = document.getElementById("<?PHP echo $textarea_id; ?>");
<?PHP
		/* Enable or disable link to comment author */
		if ($author_link == "1")
		{
?>
  oauthor = '<a href="#<?PHP echo($anchor_prefix) ?>' + id + '" title="<?PHP echo _e("Go to comment of this author", 'cf_comment_toolbar' ); ?>">' + oauthor + '</a>';
<?PHP } ?>
  otextCommentArea.value += "<?PHP echo $en_start; ?>@ " + oauthor + "<?PHP echo $en_end; ?>:\n";
  otextCommentArea.focus();
}
//-->
</script> 	
<?PHP
		/* Enable or disable smooth scrolling */
		if ($scrolling == "1" && !function_exists(paged_comments_show_all) && get_option('page_comments') != 1)
		{
?>
<script type="text/javascript" src="<?PHP echo(get_option('siteurl').'/'.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__))) ?>/smoothscroll.js"></script>
<?PHP
		}
	}
	return $body;	
}

function cf_comment_toolbar($content = '')
{
	global $comment;
	
	/* if comments are closed, or comment is tracback or pingback, */
	/* or comment is not approved, or is admin page, then exit     */
	if (!comments_open() || is_admin() || $comment->comment_approved == 0 ||
		$comment->comment_type == "trackback" || $comment->comment_type == "pingback" || get_option('thread_comments') == 1) return $content;
	
	/* typecasting local variables and loading plug-in properties */ 
	$imgpath = get_option('siteurl').'/'.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__))."/images/";
	$ID = (string)$comment->comment_ID;
	$Post_ID = (string)$comment->comment_post_ID;
	$Author = str_replace("\"","&quot;",addslashes($comment->comment_author)); /* filtering ' and " */
	$lbl_reply = (string)get_option('cf_comment_toolbar_lbl_reply');
	$lbl_quote = (string)get_option('cf_comment_toolbar_lbl_quote');
	$position = (string)get_option('cf_comment_toolbar_position');
	$style = (string)get_option('cf_comment_toolbar_style');
	$align = (string)get_option('cf_comment_toolbar_align');	
	$separator = (string)get_option('cf_comment_toolbar_separator');
	$navigation = (string)get_option('cf_comment_toolbar_navigation');
	$anchor_prefix = (string)get_option('cf_comment_toolbar_anchor_prefix');
	$textarea_id = get_option('cf_comment_toolbar_textarea_id');
		
	$new_content = "<span id=\"co_".$ID."\">".$content."</span>";
	
	/* creating toolbar */
	$toolbar = "<div class=\"comment-toolbar\" ";
	if ($align != "css") $toolbar .= "style=\"text-align: ".$align."\"";
	$toolbar .= ">";
	
	/* adding comment navigation links */
	if ($navigation == "1" && !function_exists(paged_comments_show_all) && get_option('page_comments') != 1) 
	{
		$approved_comments = get_approved_comments($Post_ID);
		$post_permalink = get_permalink($Post_ID);
		
		if (Count($approved_comments) > 1)
		{
			/* Searching the comment position */
			$cur_pos_comment = 0;
			for ($i=0; $i < Count($approved_comments); $i++)
			{
				if ($approved_comments[$i]->comment_ID == $ID) 
				{
					$cur_pos_comment = $i; break;
				}
			}
			/* Print First and Previous comment button (if present) */
			if ($cur_pos_comment >= 1) 
			{
				if ($style == "L")
					$toolbar .= "<a href=\"#".$anchor_prefix.$approved_comments[0]->comment_ID."\" title=\"First comment\">&lt;&lt;</a>";
				else if ($style == "B")
					$toolbar .= "<input type=\"button\" value=\"&lt;&lt;\" onclick=\"javascript:void(document.location.href='".$post_permalink."#".$anchor_prefix.$approved_comments[0]->comment_ID."')\" />";
					else
						$toolbar .= "<a href=\"#".$anchor_prefix.$approved_comments[0]->comment_ID."\" title=\"First comment\"><img src=\"".$imgpath."first.png\" border=\"0\" onmouseover=\"this.src='".$imgpath."first_over.png';\" onmouseout=\"this.src='".$imgpath."first.png';\"/></a>";
				if ($separator != '' && $style == "L")
					$toolbar .= ' '.$separator.' ';
				if ($style == "L")
					$toolbar .= "<a href=\"#".$anchor_prefix.$approved_comments[$cur_pos_comment - 1]->comment_ID."\" title=\"Previous comment\">&lt;</a>";
				else if ($style == "B")
					$toolbar .= "<input type=\"button\" value=\"&lt;\" onclick=\"javascript:void(document.location.href='".$post_permalink."#".$anchor_prefix.$approved_comments[$cur_pos_comment - 1]->comment_ID."')\" />";				
					else
						$toolbar .= "<a href=\"#".$anchor_prefix.$approved_comments[$cur_pos_comment - 1]->comment_ID."\" title=\"Previous comment\"><img src=\"".$imgpath."previous.png\" border=\"0\" onmouseover=\"this.src='".$imgpath."previous_over.png';\" onmouseout=\"this.src='".$imgpath."previous.png';\"/></a>";					
				if (($lbl_reply != '' || $lbl_quote != '') && $separator != '' && $style == "L")
					$toolbar .= ' '.$separator.' ';
			}
			/* Print Next and Last comment button (if present) */
			if ($cur_pos_comment < Count($approved_comments) - 1) 
			{
				if ($style == "L")
					$toolbar .= "<a href=\"#".$anchor_prefix.$approved_comments[$cur_pos_comment + 1]->comment_ID."\" title=\"Next comment\">&gt;</a>";
				else if ($style == "B")
					$toolbar .= "<input type=\"button\" value=\"&gt;\" onclick=\"javascript:void(document.location.href='".$post_permalink."#".$anchor_prefix.$approved_comments[$cur_pos_comment + 1]->comment_ID."')\" />";
					else
						$toolbar .= "<a href=\"#".$anchor_prefix.$approved_comments[$cur_pos_comment + 1]->comment_ID."\" title=\"Next comment\"><img src=\"".$imgpath."next.png\" border=\"0\" onmouseover=\"this.src='".$imgpath."next_over.png';\" onmouseout=\"this.src='".$imgpath."next.png';\"/></a>";																
				if ($separator != '' && $style == "L")
					$toolbar .= ' '.$separator.' ';
				if ($style == "L")
					$toolbar .= "<a href=\"#".$anchor_prefix.$approved_comments[Count($approved_comments) - 1]->comment_ID."\" title=\"Last comment\">&gt;&gt;</a>";
				else if ($style == "B")
					$toolbar .= "<input type=\"button\" value=\"&gt;&gt;\" onclick=\"javascript:void(document.location.href='".$post_permalink."#".$anchor_prefix.$approved_comments[Count($approved_comments) - 1]->comment_ID."')\" />";		
					else
						$toolbar .= "<a href=\"#".$anchor_prefix.$approved_comments[Count($approved_comments) - 1]->comment_ID."\" title=\"Last comment\"><img src=\"".$imgpath."last.png\" border=\"0\" onmouseover=\"this.src='".$imgpath."last_over.png';\" onmouseout=\"this.src='".$imgpath."last.png';\"/></a>";					
				if (($lbl_reply != '' || $lbl_quote != '') && $separator != '' && $style == "L")
					$toolbar .= ' '.$separator.' ';
			}
		}		
	}
		
	/* adding reply and quote links */	
	if ($lbl_reply != '')
	{
		if ($style == "L")
			$toolbar .= "<a href=\"#".$textarea_id."\" onclick=\"CF_Reply('".$ID."','".$Author."'); return false;\">".$lbl_reply."</a>";
		else if ($style == "B")
			$toolbar .= "<input type=\"button\" value=\"".$lbl_reply."\" onclick=\"CF_Reply('".$ID."','".$Author."');\" />";
			else 
				$toolbar .= "<a href=\"#".$textarea_id."\" onclick=\"CF_Reply('".$ID."','".$Author."'); return false;\" title=\"".$lbl_reply."\"><img src=\"".$imgpath."reply.png\" border=\"0\" onmouseover=\"this.src='".$imgpath."reply_over.png';\" onmouseout=\"this.src='".$imgpath."reply.png';\"/></a>";
	}
	if ($lbl_quote != '')
	{
		if ($lbl_reply != '' && $separator != '' && $style == "L")
			$toolbar .= ' '.$separator.' ';
		if ($style == "L")
			$toolbar .= "<a href=\"#".$textarea_id."\" onclick=\"CF_Quote('".$ID."','".$Author."'); return false;\">".$lbl_quote."</a>";
		else if ($style == "B")
			$toolbar .= "<input type=\"button\" value=\"".$lbl_quote."\" onclick=\"CF_Quote('".$ID."','".$Author."');\" />";
			else
				$toolbar .= "<a href=\"#".$textarea_id."\" onclick=\"CF_Quote('".$ID."','".$Author."'); return false;\" title=\"".$lbl_quote."\"><img src=\"".$imgpath."quote.png\" border=\"0\" onmouseover=\"this.src='".$imgpath."quote_over.png';\" onmouseout=\"this.src='".$imgpath."quote.png';\"/></a>";
	}
	$toolbar .= "</div>";
	
	if ($position == 'T')
		$new_content = $toolbar.$new_content;
	else
		$new_content = $new_content.$toolbar;
	
	return $new_content;
}
?>