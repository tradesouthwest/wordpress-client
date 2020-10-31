<?php

global $current_user, $post;
$current_user = wp_get_current_user();
$dashboard_profile_link = hireo_get_template_link('template/user_dashboard.php');
$dashboard_membership_link = hireo_get_template_link('template/user_dashboard_membership.php');
$dashboard_bookmarks = hireo_get_template_link('template/user_dashboard_bookmarks.php');
$dashboard_saved_search = hireo_get_template_link('template/user_dashboard_saved_search.php');
$dashboard_email_alerts = hireo_get_template_link('template/user_dashboard_email_alerts.php');
$home_link = home_url('/');

$user_profile = $user_membership = $user_bookmarks = $user_saved_search = $user_email_alerts = '';
if (is_page_template('template/user_dashboard.php')) {
	$user_profile = 'class=active';
} elseif (is_page_template('template/user_dashboard_bookmarks.php')) {
	$user_bookmarks = 'class=active';
} elseif (is_page_template('template/user_dashboard_saved_search.php')) {
	$user_saved_search = 'class=active';
} elseif (is_page_template('template/user_dashboard_membership.php')) {
	$user_membership = 'class=active';
} elseif(is_page_template('template/user_dashboard_email_alerts.php')) {
	$user_email_alerts = 'class=active';
}
?>

<!-- Dashboard Sidebar
		================================================== -->
<div class="dashboard-sidebar">
	<div class="dashboard-sidebar-inner" data-simplebar>
		<div class="dashboard-nav-container">

			<!-- Responsive Navigation Trigger -->
			<a href="#" class="dashboard-responsive-nav-trigger">
					<span class="hamburger hamburger--collapse" >
						<span class="hamburger-box">
							<span class="hamburger-inner"></span>
						</span>
					</span>
				<span class="trigger-title">Dashboard Navigation</span>
			</a>

			<!-- Navigation -->
			<div class="dashboard-nav">
				<div class="dashboard-nav-inner">

					<ul data-submenu-title="Account">

						<?php if (!empty($dashboard_bookmarks)) {
							echo '<li ' .esc_attr( $user_bookmarks ). '><a href="' . $dashboard_bookmarks .  '"><i class="icon-material-outline-star-border"></i>' . esc_html__('Bookmarks', 'hireo-theme') . '</a></li>';
						} ?>
						<?php if (!empty($dashboard_saved_search)) {
							echo '<li ' .esc_attr( $user_saved_search ). '><a href="' . $dashboard_saved_search .  '"><i class="icon-material-outline-find-in-page"></i>' . esc_html__('Saved searches', 'hireo-theme') . '</a></li>';
						} ?>
						<?php if (!empty($dashboard_email_alerts)) {
							echo '<li ' .esc_attr( $user_email_alerts ). '><a href="' . $dashboard_email_alerts .  '"><i class="icon-material-outline-notifications"></i>' . esc_html__('Email alerts', 'hireo-theme') . '</a></li>';
						} ?>
						<?php if (!empty($dashboard_membership_link)) {
							echo '<li ' .esc_attr( $user_membership ). '><a href="' . $dashboard_membership_link .  '"><i class="icon-material-outline-dashboard"></i>' . esc_html__('Orders', 'hireo-theme') . '</a></li>';
						} ?>
						<?php
						if (!empty($dashboard_profile_link)) {
							echo '<li ' .esc_attr( $user_profile ). '><a href="' . esc_url($dashboard_profile_link) . '"><i class="icon-material-outline-settings"></i>' . esc_html__('Settings', 'hireo-theme') . '</a></li>';
						}
						?>
						<li><a href="<?php echo wp_logout_url(home_url('/')) ?>"><i class="icon-material-outline-power-settings-new"></i> <?php echo esc_html__('Logout', 'hireo-theme'); ?></a></li>
					</ul>

				</div>
			</div>
			<!-- Navigation / End -->

		</div>
	</div>
</div>
<!-- Dashboard Sidebar / End -->
