<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after. Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?>
	<!-- #main -->
    <div class="footer">
		<span class="social">Follow us on</span>
		<ul class="social-links">
			<?php wp_nav_menu('menu=social'); ?>
		</ul>
		<div class="footer-links">
			<h3>Denver Virtual Offices</h3>
			<ul>
				<?php wp_nav_menu('menu=dvo-footer-links'); ?>
			</ul>
		</div>
		<div class="footer-links">
			<h3>Company</h3>
			<ul>
				<?php wp_nav_menu('menu=company-footer-links'); ?>
			</ul>
		</div>
		<div class="footer-links">
			<h3>Products</h3>
			<ul>
				<?php wp_nav_menu('menu=products-footer-links'); ?>
			</ul>
		</div>
		<span class="copyrights">&copy; 2011 DVO</span>
		<ul class="fb-attendance">
			<li class="fb-likes">
				<a href="#"></a>
			</li>
			<li class="attendance-fb">
				<span>3.012 people likes it</span>
			</li>
		</ul>
	</div>
</div>

<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
</body>
</html>