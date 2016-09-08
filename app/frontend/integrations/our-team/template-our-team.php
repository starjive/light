<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Our Team Template
 * Displays team members using the sf_our_team action.
 *
 * @see sf_display_our_team()
 */
?>

<?php if ( class_exists( 'Woothemes_Our_Team' ) ) { ?>

	<section class="our-team">

		<h1 class="section-title"><?php _e( 'Our Team', 'sfwp-locale' ); ?></h1>

		<?php

		$limit 		= apply_filters( 'sf_template_our_team_limit', $team_limit = 3 );
		$columns 	= apply_filters( 'sf_template_our_team_columns', $team_columns = 3 );

		do_action( 'sf_our_team', apply_filters( 'sf_template_our_team_args', array(
			'limit' 	=> $limit,
			'per_row' 	=> $columns
			) )
		);

		?>

	</section>

<?php } ?>