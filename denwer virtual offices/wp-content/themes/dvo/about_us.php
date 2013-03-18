<?php
/*
Template Name: About_Us
*/

get_header(); ?>

	<div class="content">
		<?php
			$content_post56 = get_post('56');
			echo $content_post56->post_content;
		?>
	</div>

<?php get_footer('about_us'); ?>
