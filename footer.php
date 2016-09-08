<?php
/**
 * Footer Template
 *
 * Here we setup all logic and XHTML that is required for the footer section of all screens.
 *
 * @subpackage Template
 */

global $sf_options;
sf_footer_top();
?>

	<?php sf_footer_before(); ?>
	<footer id="footer" class="col-full">

		<?php sf_footer_inside(); ?>

		<div id="copyright" class="col-left">
			<?php sf_footer_left(); ?>
		</div>

		<div id="credit" class="col-right">
			<?php sf_footer_right(); ?>
		</div>

	</footer>
	<?php sf_footer_after(); ?>

	</div><!-- /#inner-wrapper -->

</div><!-- /#wrapper -->

<div class="fix"></div><!--/.fix-->

<?php wp_footer(); ?>
<?php sf_foot(); ?>
</body>
</html>