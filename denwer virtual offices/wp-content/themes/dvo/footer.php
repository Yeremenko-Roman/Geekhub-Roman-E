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
			<!--<li class="twit">
				<a href="#"></a>
			</li>
			<li class="fb">
				<a href="#"></a>
			</li>
			<li class="rss">
				<a href="#"></a>
			</li>-->
		</ul>
		<span class="notation">Need more than a Virtual Office? check out our Executive Suites button to link to another site.</span>
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