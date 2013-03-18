<?php
/*
Template Name: Home
*/
get_header('home'); ?>

    <div class="content">
		<?php 
			$post_id_52 = get_post('52'); 
			echo $post_id_52->post_content;					
		?>
	</div>
		
<?php get_footer(); ?>
