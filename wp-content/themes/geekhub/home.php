<?php
/*
Template Name: Home
*/
get_header('home'); ?>
		
		<p>	<?php 
				$post_id_65 = get_post('65'); 
				echo $post_id_65->post_content;					
			?>
		</p>
		<h3><?php
				$cat_id_7 = get_cat_name('7'); 
				echo $cat_id_7; ?>
		</h3>	
		<?php 
			$post_id_71 = get_post('71'); 
			echo $post_id_71->post_content;					
		?>

<?php get_footer(); ?>
