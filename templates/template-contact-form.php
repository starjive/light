<?php
/**
 * Template Name: Contact Form
 *
 * The contact form page template displays the a
 * simple contact form in your website's content area.
 *
 * @subpackage Template
 */

get_header();
?>

<?php
global $sf_options;

$title_before = '<h1 class="title entry-title">';
$title_after = '</h1>';

$nameError = '';
$emailError = '';
$commentError = '';
$mathCheck = '';

//If the form is submitted
if( isset( $_POST['submitted'] ) ) {

	//Check to see if the honeypot captcha field was filled in
	if( trim( $_POST['checking'] ) !== '' ) {
		$captchaError = true;
	} else {

		// Check math field
		if( $_POST['mathCheck'] != 9 && strcasecmp( $_POST['mathCheck'], 'nine' ) != 0  ) {
			$mathCheck = __( 'You got the maths wrong.', 'sfwp-locale' );
			$hasError = true;
		} else {
			$math = trim( $_POST['mathCheck'] );
		}

		//Check to make sure that the name field is not empty
		if( trim( $_POST['contactName'] ) === '' ) {
			$nameError =  __( 'You forgot to enter your name.', 'sfwp-locale' );
			$hasError = true;
		} else {
			$name = strip_tags( trim( $_POST['contactName'] ) );
		}

		//Check to make sure sure that a valid email address is submitted
		if( trim( $_POST['email'] ) === '' )  {
			$emailError = __( 'You forgot to enter your email address.', 'sfwp-locale' );
			$hasError = true;
		} else if ( ! is_email( sanitize_email( $_POST['email'] ) ) ) {
			$emailError = __( 'You entered an invalid email address.', 'sfwp-locale' );
			$hasError = true;
		} else {
			$email = sanitize_email( trim( $_POST['email'] ) );
		}

		//Check to make sure comments were entered
		if( trim( $_POST['comments'] ) === '' ) {
			$commentError = __( 'You forgot to enter your comments.', 'sfwp-locale' );
			$hasError = true;
		} else {
			$comments = sanitize_text_field( stripslashes( trim( $_POST['comments'] ) ) );
		}

		//If there is no error, send the email
		if ( ! isset( $hasError ) ) {

			$emailTo = get_option( 'sf_contactform_email' );
			$subject = __( 'Contact Form Submission from ', 'sfwp-locale' ).$name;

			$sendCopy = false;
			if ( isset( $_POST['sendCopy'] ) && $_POST['sendCopy'] !== '' ) {
				$sendCopy = true;
			}

			$body = sprintf( __( 'Name: %s \n\nEmail: %s \n\nComments: %s', 'sfwp-locale' ), $name, $email, $comments );
			$headers = __( 'From: ', 'sfwp-locale' ) . "$name <$email>" . "\r\n" . __( 'Reply-To: ', 'sfwp-locale' ) . $email;

			wp_mail( $emailTo, $subject, $body, $headers );

			if ( $sendCopy == true ) {

				$nameTo = get_option( 'sf_contact_title' );
				if ( '' == trim( $nameTo ) ) {
					$nameTo = get_bloginfo( 'name' );
				}
				$nameTo = strip_tags( trim( $nameTo ) );
				$subject = __( 'You emailed ', 'sfwp-locale' ) . get_bloginfo( 'title' );
				$headers = __( 'From: ', 'sfwp-locale' ) . "$nameTo <$emailTo>";
				wp_mail( $email, $subject, $body, $headers );
			}

			$emailSent = true;

		}
	}
}
?>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
jQuery(document).ready(function() {
	jQuery( 'form#contactForm').submit(function() {
		jQuery( 'form#contactForm .error').remove();
		var hasError = false;
		jQuery( '.requiredField').each(function() {
			if(jQuery(this).hasClass('math')) {
				if( jQuery.trim(jQuery(this).val()) != 9 && jQuery.trim(jQuery(this).val()).toLowerCase() != 'nine' ) {
					jQuery(this).parent().append( '<span class="error"><?php _e( 'You got the maths wrong', 'sfwp-locale' ); ?>.</span>' );
					jQuery(this).addClass( 'inputError' );
					hasError = true;
				}
			} else {
				if(jQuery.trim(jQuery(this).val()) == '') {
					var labelText = jQuery(this).prev( 'label').text();
					jQuery(this).parent().append( '<span class="error"><?php _e( 'You forgot to enter your', 'sfwp-locale' ); ?> '+labelText+'.</span>' );
					jQuery(this).addClass( 'inputError' );
					hasError = true;
				} else if(jQuery(this).hasClass( 'email')) {
					var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
					if(!emailReg.test(jQuery.trim(jQuery(this).val()))) {
						var labelText = jQuery(this).prev( 'label').text();
						jQuery(this).parent().append( '<span class="error"><?php _e( 'You entered an invalid', 'sfwp-locale' ); ?> '+labelText+'.</span>' );
						jQuery(this).addClass( 'inputError' );
						hasError = true;
					}
				}
			}
		});
		if(!hasError) {
			var formInput = jQuery(this).serialize();
			jQuery.post(jQuery(this).attr( 'action'),formInput, function(data){
				jQuery( 'form#contactForm').slideUp( "fast", function() {
					jQuery(this).before( '<?php echo do_shortcode( '[box type="tick"]' . __( '<strong>Thanks!</strong> Your email was successfully sent.', 'sfwp-locale' ) . '[/box]' ); ?>' );
				});
			});
		}

		return false;

	});
});
//-->!]]>
</script>

   <?php sf_content_before(); ?>
    <div id="content" class="col-full">

        <?php
        	// Output Google Map
	        if ( isset( $sf_options['sf_contactform_map_coords'] )  && '' != $sf_options['sf_contactform_map_coords'] ) {
	        	sf_maps_contact_output( "geocoords=" . $sf_options['sf_contactform_map_coords'] );
				echo do_shortcode( '<br>' );
			}
		?>

		<div id="main-sidebar-container">

		<!-- #main Starts -->
		<?php sf_main_before(); ?>
		<section id="main">

		<?php sf_loop_before(); ?>
		<!-- Post Starts -->
		<?php sf_post_before(); ?>

            <div id="contact-page" class="page">

            <?php sf_post_inside_before(); ?>

			<header>
				<?php the_title( $title_before, $title_after ); ?>
			</header>

            <?php if( isset( $emailSent ) && $emailSent == true ) { ?>

                <p class="info"><?php _e( 'Your email was successfully sent.', 'sfwp-locale' ); ?></p>

            <?php } else { ?>

                <?php if ( have_posts() ) { ?>

                <?php while ( have_posts() ) { the_post(); ?>

                        <section class="entry">
	                        <?php the_content(); ?>

                			<div class="contact-information">
    							<?php if ( isset( $sf_options['sf_contact_panel'] ) && $sf_options['sf_contact_panel'] == 'true' ) { ?>
						    	<section id="office-location"<?php if ( ( isset($sf_options['sf_contact_subscribe_and_connect']) && $sf_options['sf_contact_subscribe_and_connect'] == 'true' ) ) { ?> class="col-left"<?php } ?>>
									<?php if (isset($sf_options['sf_contact_title'])) { ?><h3><?php echo stripslashes( $sf_options['sf_contact_title'] ); ?></h3><?php } ?>
									<ul>
										<?php if (isset($sf_options['sf_contact_title']) && $sf_options['sf_contact_title'] != '' ) { ?><li><?php echo stripslashes( $sf_options['sf_contact_address'] ); ?></li><?php } ?>
										<?php if (isset($sf_options['sf_contact_number']) && $sf_options['sf_contact_number'] != '' ) { ?><li><?php _e( 'Tel:', 'sfwp-locale' ); ?> <?php echo $sf_options['sf_contact_number']; ?></li><?php } ?>
										<?php if (isset($sf_options['sf_contact_fax']) && $sf_options['sf_contact_fax'] != '' ) { ?><li><?php _e( 'Fax:', 'sfwp-locale' ); ?> <?php echo $sf_options['sf_contact_fax']; ?></li><?php } ?>
									</ul>
						    	</section>
						    	<?php } ?>
						    	<div class="contact-social<?php if ( ( isset( $sf_options['sf_contact_panel'] ) && $sf_options['sf_contact_panel'] == 'true' ) || ( isset($sf_options['sf_contact_subscribe_and_connect']) && $sf_options['sf_contact_subscribe_and_connect'] == 'true' ) )  { ?> col-right<?php } ?>">

						    		<?php if ( isset($sf_options['sf_contact_subscribe_and_connect']) && $sf_options['sf_contact_subscribe_and_connect'] == 'true' ) { sf_subscribe_connect( 'true' ); } ?>

						    	</div>

						    	<div class="clear"></div>

						    	</div><!-- /.contact-information -->

                        </section>

                    <?php if( isset( $hasError ) || isset( $captchaError ) ) { ?>
                        <p class="alert"><?php _e( 'There was an error submitting the form.', 'sfwp-locale' ); ?></p>
                    <?php } ?>

                    <?php if ( get_option( 'sf_contactform_email' ) == '' ) { ?>
                        <?php echo do_shortcode( '[box type="alert"]' . __( 'Please <strong>add your e-mail</strong> in <em>Contact Page > Contact Form E-mail</em>.', 'sfwp-locale' ) . '[/box]' );  ?>
                    <?php } ?>


                    <form action="<?php the_permalink(); ?>" id="contactForm" method="post">

                        <ol class="forms">
                            <li><label for="contactName"><?php _e( 'Name', 'sfwp-locale' ); ?></label>
                                <input type="text" name="contactName" id="contactName" value="<?php if( isset( $_POST['contactName'] ) ) { echo esc_attr( $_POST['contactName'] ); } ?>" class="txt requiredField" />
                                <?php if($nameError != '') { ?>
                                    <span class="error"><?php echo $nameError;?></span>
                                <?php } ?>
                            </li>

                            <li><label for="email"><?php _e( 'Email', 'sfwp-locale' ); ?></label>
                                <input type="text" name="email" id="email" value="<?php if( isset( $_POST['email'] ) ) { echo esc_attr( $_POST['email'] ); } ?>" class="txt requiredField email" />
                                <?php if($emailError != '') { ?>
                                    <span class="error"><?php echo $emailError;?></span>
                                <?php } ?>
                            </li>

                            <li class="textarea"><label for="commentsText"><?php _e( 'Message', 'sfwp-locale' ); ?></label>
                                <textarea name="comments" id="commentsText" rows="20" cols="30" class="requiredField"><?php if( isset( $_POST['comments'] ) ) { echo esc_textarea( $_POST['comments'] ); } ?></textarea>
                                <?php if( $commentError != '' ) { ?>
                                    <span class="error"><?php echo $commentError; ?></span>
                                <?php } ?>
                            </li>

                            <li><label for="mathCheck"><?php _e( 'Solve:', 'sfwp-locale' ); ?> 3 + 6</label>
                                <input type="text" name="mathCheck" id="mathCheck" value="<?php if( isset( $_POST['mathCheck'] ) ) { echo esc_attr( $_POST['mathCheck'] ); } ?>" class="txt requiredField math" />
                                <?php if($mathCheck != '') { ?>
                                    <span class="error"><?php echo $mathCheck;?></span>
                                <?php } ?>
                            </li>

                            <li class="inline"><input type="checkbox" name="sendCopy" id="sendCopy" value="true"<?php if( isset( $_POST['sendCopy'] ) && $_POST['sendCopy'] == true ) { echo ' checked="checked"'; } ?> /><label for="sendCopy"><?php _e( 'Send a copy of this email to yourself', 'sfwp-locale' ); ?></label></li>
                            <li class="screenReader"><label for="checking" class="screenReader"><?php _e( 'If you want to submit this form, do not enter anything in this field', 'sfwp-locale' ); ?></label><input type="text" name="checking" id="checking" class="screenReader" value="<?php if( isset( $_POST['checking'] ) ) { echo esc_attr( $_POST['checking'] ); } ?>" /></li>
                            <li class="buttons"><input type="hidden" name="submitted" id="submitted" value="true" /><input class="submit button" type="submit" value="<?php esc_attr_e( 'Submit', 'sfwp-locale' ); ?>" /></li>
                        </ol>
                    </form>

                    <?php
                    		} // End WHILE Loop
                    	}
                    }
                    ?>
                    <div class="fix"></div>
				<?php sf_post_inside_after(); ?>

            </div><!-- /#contact-page -->

           <?php sf_post_after(); ?>

            </section><!-- /#main -->
            <?php sf_main_after(); ?>

            <?php get_sidebar(); ?>

		</div><!-- /#main-sidebar-container -->

		<?php get_sidebar( 'alt' ); ?>

    </div><!-- /#content -->
	<?php sf_content_after(); ?>

<?php get_footer(); ?>