<?php
/*
Template Name: User Dashboard Profile Page
 */

global $current_user;
wp_get_current_user();
$userID                 = $current_user->ID;
$user_login             = $current_user->user_login;
$username               = get_the_author_meta( 'user_login' , $userID );
$first_name             = get_the_author_meta( 'first_name' , $userID );
$last_name              = get_the_author_meta( 'last_name' , $userID );
$user_email             = get_the_author_meta( 'user_email' , $userID );
$dashboard_profile_link = hireo_get_dashboard_profile_link();
$user_custom_picture = get_the_author_meta( 'thechamp_large_avatar' , $userID );
$author_picture_id      =   get_the_author_meta( 'hireo_author_picture_id' , $userID );
if($user_custom_picture== ''){
	$user_custom_picture = get_template_directory_uri().'/images/user-avatar-placeholder.png';
}
$current_user_meta = get_user_meta( $userID );
$user_data              =   get_userdata( $userID );
get_header(); ?>

	<!-- Dashboard Container -->
	<div class="dashboard-container">

		<?php get_template_part('template-parts/dashboard', 'sidebar'); ?>


	<!-- Dashboard Content
================================================== -->
	<div class="dashboard-content-container" data-simplebar>
		<div class="dashboard-content-inner" >

			<!-- Dashboard Headline -->
			<div class="dashboard-headline">
				<h3><?php the_title(); ?></h3>

				<!-- Breadcrumbs -->
				<?php if ( function_exists( 'dimox_breadcrumbs' ) ) dimox_breadcrumbs(); ?>
			</div>

		<form method="post" id="user_inform">
			<!-- Row -->
			<div class="row">

				<!-- Dashboard Box -->
				<div class="col-xl-12">
					<div class="dashboard-box margin-top-0">

						<!-- Headline -->
						<div class="headline">
							<h3><i class="icon-material-outline-account-circle"></i> My Account</h3>
						</div>

						<div class="content with-padding padding-bottom-0">

							<div class="row">
								<div id="hireo_upload_errors"></div>
								<div id="snackbar-user"></div><!-- Begin addon -->
<div class="col-auto profile-addon"><?php echo hireo_child_profile_dashboard_addon(); ?></div>
			 <!-- Ends addon --><div class="col-auto">
									<div id="hireo_profile_photo">
										<div class="hireo-thumb">
											<div class="avatar-wrapper">
												<?php if (!empty( $author_picture_id )) {
													$author_picture_id = intval( $author_picture_id );
													if ($author_picture_id) {
														echo wp_get_attachment_image($author_picture_id, array(150, 150));
														echo '<input type="hidden" class="profile-pic-id" id="profile-pic-id" name="profile-pic-id" value="' . esc_attr( $author_picture_id ).'"/>';
													}
												} else {
													print '<img class="profile-pic" id="profile-image" src="'.esc_url($user_custom_picture).'" alt="user image">';
												} ?>
											</div>
										</div>
									</div>
									<div class="profile-img-controls">
										<div id="plupload-container"></div>
									</div>
									<div id="profile_upload_container">
										<a id="select_user_profile_photo" class="upload-button" href="javascript:;" data-tippy-placement="bottom" title="Change Avatar"></a>
									</div>
								</div>

								<div class="col">
									<div class="row">

										<div class="col-xl-6">
											<div class="submit-field">
												<h5>First Name</h5>
												<input type="text" name="firstname" id="firstname" class="with-border" value="<?php echo esc_attr( $first_name );?>">
											</div>
										</div>

										<div class="col-xl-6">
											<div class="submit-field">
												<h5>Last Name</h5>
												<input type="text" class="with-border" name="lastname" id="lastname" value="<?php echo esc_attr( $last_name );?>">
											</div>
										</div>

										<div class="col-xl-6">
											<div class="submit-field">
												<h5>Email Address</h5>
												<input type="text" name="prof_useremail" id="prof_useremail" class="with-border" value="<?php echo esc_attr( $user_email );?>">
											</div>
										</div>

									</div>
								</div>
							</div>

						</div>
					</div>
				</div>

				<!-- Dashboard Box -->
				<div class="col-xl-12">
					<div id="test1" class="dashboard-box">

						<!-- Headline -->
						<div class="headline">
							<h3><i class="icon-material-outline-lock"></i> <?php esc_html_e( 'Password & Security', 'hireo-theme' ); ?></h3>
						</div>

						<div class="content with-padding">
							<div class="row">
								<div class="col-xl-4">
									<div class="submit-field">
										<h5><?php esc_html_e( 'Current Password', 'hireo-theme' ); ?></h5>
										<input type="password" id="oldpass" class="with-border" name="current_pass">
									</div>
								</div>

								<div class="col-xl-4">
									<div class="submit-field">
										<h5><?php esc_html_e( 'New Password', 'hireo-theme' ); ?></h5>
										<input type="password" id="newpass" class="with-border" name="new_pass">
									</div>
								</div>

								<div class="col-xl-4">
									<div class="submit-field">
										<h5><?php esc_html_e( 'Repeat New Password', 'hireo-theme' ); ?></h5>
										<input type="password" id="confirmpass" class="with-border" name="confirm_pass">
									</div>
								</div>
								<?php wp_nonce_field( 'hireo_pass_ajax_nonce', 'hireo-security-pass' );   ?>

							</div>
						</div>
					</div>
				</div>

				<!-- Button -->
				<div class="col-xl-12">
					<button type="submit" id="hireo_update_profile" class="button ripple-effect big margin-top-30"><?php esc_html_e('Save Changes', 'hireo-theme'); ?></button>
				</div>

				<!-- Delete account -->
				<div class="col-xl-12">
					<div class="dashboard-box delete_profile">
						<div class="content with-padding">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12 d-md-flex align-items-center justify-content-end">
								<p class="account-removal"><span><?php esc_html_e( 'If you would like to cancel your Membership: ', 'hireo-theme'); ?></span>	<button class="button dark ripple-effect big" id="hireo_delete_account"> <?php esc_html_e( 'Delete My Account', 'hireo-theme' ); ?> </button></p>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
			<!-- Row / End -->
		</form>

<?php
get_footer('dashboard');
