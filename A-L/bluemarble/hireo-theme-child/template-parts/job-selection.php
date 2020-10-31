<?php
$cities = array();
$countries = array();
$organizations = array();
$closing_days = array();
$posted_days = array();
$posted_dates = array();

$get_keyword = isset($_GET['get_keyword']) ? $_GET['get_keyword'] : '';
$get_organizations = isset($_GET['organization_title']) ? $_GET['organization_title'] : '';
$get_cities = isset($_GET['cities']) ? $_GET['cities'] : '';
$get_countries = isset($_GET['countries']) ? $_GET['countries'] : '';
$jobs_type = isset($_GET['jobs_type']) ? $_GET['jobs_type'] : '';
$get_regions = isset($_GET['regions']) ? $_GET['regions'] : '';
$post_ids = array();
$meta_values = array();

$current_date = current_time( 'Ymd', 0 );
$post_args = array(
	'post_type' => 'jobs-item',
	'order' => 'ASC',
	'posts_per_page' => -1,
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key' => 'closing_on_date',
			'value' => $current_date,
			'compare' => '>='
		)
	)
);

// The Query
$post_query = new WP_Query( $post_args );

// The Loop
if ( $post_query->have_posts() ) {
	while ( $post_query->have_posts() ) {
		$post_query->the_post();
		$post_ids[] = get_the_ID();
		$organization_title = get_field('organization_title');
		$job_city = get_field('city');
		$job_country = get_field('country');
		$job_expire = get_field( 'closing_on_date' );
		$job_posted = get_the_time('Y-m-d H:i:s');
		if (!empty($organization_title)) {
			if (is_array($organizations) && !in_array($organization_title, $organizations)) {
				$organizations[] = $organization_title;
			}
		}
		if (!empty($job_city)) {
			$cities[] = $job_city;
		}
		if (!empty($job_country)) {
			$countries[] = $job_country;
		}
		if (!empty($job_expire)) {
			$current_time = current_time( 'Y-m-d', 0 );
			$timestamp    = strtotime( $job_expire );
			$count_diff = null;
			if ( $job_expire > $current_time ) {
				$count_diff = ( strtotime( $job_expire ) - strtotime( $current_time ) ) / 3600 / 24;
			}
			if ($count_diff > 0) {
				$closing_days[] = $count_diff;
			}
		}
		if (!empty($job_posted)) {
			$words = array("minute", "minutes", "hour", "hours");
			foreach ($words as $item) {
				if ( strpos($job_posted, $item) !== false ) {
					$job_posted = '1 day ago';
				}
			}
			$posted_dates[] = $job_posted;
		}
		?>
	<?php }
}

foreach ($posted_dates as $posted_date) {
	$seconds_ago = (time() - strtotime($posted_date));
	$counter_days = intval($seconds_ago / 86400);
	$posted_days[] = $counter_days;
}

function trim_value(&$value)
{
	$value = trim($value);
}
array_walk($organizations, 'trim_value');
$organizations_values = array_unique($organizations);
$cities_values = array_unique($cities);
array_walk($countries, 'trim_value');
$countries_values = array_unique($countries);
$closing_values = array_unique($closing_days);
$posted_values = array_unique($posted_days);

?>

<div id="filter_sidebared" class="col-xl-3 col-lg-4">
	<div class="filter-overlay"></div>
	<div class="filter_mobile">
		<i class="icon-line-awesome-filter"></i>
		<span><?php _e('Filter', 'hireo-theme'); ?></span>
	</div>
	<div class="sidebar-container">
		<a href="#" class="close_filter mobile_only_show"><i class="icon-line-awesome-close"></i></a>
        <div class="footer_buttons mobi_only">
			<input type="reset" value="Clear Filters" class="clear_filter mobile_show">
			<a href="0#" class="show_filter mobi_only"><?php _e('Show results', 'hireo-theme'); ?></a>
		</div>
		<a href="#" class="show_filter"><?php _e('Show results', 'hireo-theme'); ?></a>
		<!-- Keywords -->
		<div class="sidebar-widget">
			<h3>Keywords</h3>
			<div class="keywords-container">
				<div class="keyword-input-container">
					<select class="selectpicker default choice-filter choice-filter-keywords" multiple name="keywords[]"></select>
					<input type="text" class="keyword-input" placeholder="e.g. job title"/>
					<a class="keyword-input-button ripple-effect" onclick="fetch_click()"> <i class="icon-material-outline-add"></i></a>
				</div>
				<div class="keywords-list"><!-- keywords go here --><?php
					if (!empty($get_keyword)) :
						$keyword_arr = str_replace(';', ' ', $get_keyword);
						$keyword_exist = explode(' ', $keyword_arr);
						$clear_keywords = array_pop($keyword_exist);
						if (is_array($keyword_exist)) {
							foreach ($keyword_exist as $keyword) { ?>
								<span class="keyword"><span class="keyword-remove""></span><span class="keyword-text"><?php echo str_replace(';', '', $keyword); ?></span></span>
							<?php }
						} else { ?>
							<span class="keyword"><span class="keyword-remove""></span><span class="keyword-text"><?php echo str_replace(';', '', $get_keyword); ?></span></span>
						<?php }
						?>
					<?php endif; ?></div>
				<input type="hidden" name="keyword" class="hidden_value" value="">
				<input type="hidden" name="get_keyword" value="<?php if(!empty($_GET['keywords'])) { echo $_GET['keywords'];} ?>">
				<div class="clearfix"></div>
			</div>
		</div>

		<div class="productcategory__filters filter-col" id="jobs_filter">

			<!-- Organization -->
			<div class="sidebar-widget" style="position: relative">
				<h3>Organization</h3>
				<?php if (!empty($organizations_values) && is_array($organizations_values)) {
					sort($organizations_values);
					?>
					<select class="selectpicker default choice-filter choice-filter-organization_title" name="organization_title[]" multiple data-live-search="true" data-actions-box="true" title="Select Organization" >
						<?php foreach ($organizations_values as $organizations_value) {
							$get_organizations = isset($_GET['organization_title']) ? $_GET['organization_title'] : '';
							$get_organizations = explode(',', $get_organizations);
							?>
							<option value="<?php echo $organizations_value; ?>" <?php if (!empty($get_organizations) && in_array($organizations_value, $get_organizations)){echo 'selected';} ?>><?php echo $organizations_value; ?></option>
						<?php } ?>
					</select>
					<button class="filterBtn" data-parent="organization_title" data-touch="1">Apply</button>
				<?php } ?>
			</div>

			<!-- Job Types -->
			<div class="sidebar-widget">
				<h3>Job Type</h3>
				<?php

				$term_ids = array();
				$get_type_taxonomies = array();
				foreach ($post_ids as $simple_id) {
					$get_type_taxonomies[] = get_the_terms($simple_id, 'jobs_types');
				}

				if (!empty($get_type_taxonomies) && is_array($get_type_taxonomies)) {
					$get_type_taxonomies = array_filter($get_type_taxonomies);
					foreach ($get_type_taxonomies as $get_type_taxonomy) {
						$term_ids[] = $get_type_taxonomy[0]->term_id;
					}
				}

				$term_ids = array_unique($term_ids);

				$type_taxonomies = get_terms( array(
					'hide_empty'    => 0,
					'taxonomy'      => 'jobs_types',
					'include'       => $term_ids,
					'orderby'       => 'include',
				) );

				if (is_array($type_taxonomies)) { ?>
					<div class="switches-list">
						<?php foreach ( $type_taxonomies as $type_taxonomy ) {
							$get_job_type = isset($_GET['jobs_type']) ? $_GET['jobs_type'] : '';
							$get_job_type = explode(',', $get_job_type);
							?>
							<div class="switch-container">
								<label class="switch"><input type="checkbox" class="choice-filter choice-filter-jobs_type" name="jobs_type[]" value="<?php echo $type_taxonomy->slug; ?>" <?php if (!empty($get_job_type) && in_array($type_taxonomy->slug, $get_job_type)){ echo 'checked';} ?>><span
											class="switch-button"></span> <?php echo $type_taxonomy->name; ?></label>
							</div>
						<?php } ?>
					</div>
				<?php } ?>


			</div>

			<!-- Region -->
			<div class="sidebar-widget">
				<h3>Region</h3>
				<?php
				$region_ids = array();
				if (empty($get_regions) && !empty($post_ids)) {
					$get_region_taxonomies = array();
					foreach ($post_ids as $simple_id) {
						$get_region_taxonomies[] = get_the_terms($simple_id, 'region');
					}
				}
				if (!empty($get_region_taxonomies) && is_array($get_region_taxonomies)) {
					$get_region_taxonomies = array_filter($get_region_taxonomies);
					foreach ($get_region_taxonomies as $get_region_taxonomy) {
						$region_ids[] = $get_region_taxonomy[0]->term_id;
					}
				}
				$region_ids = array_unique($region_ids);

				$region_taxonomies = get_terms( array(
					'hide_empty'    => 0,
					'taxonomy'      => 'region',
					'include'       => $region_ids,
					'order'         => 'ASC',
				) );
				if (is_array($region_taxonomies)) { ?>
					<div class="switches-list">
						<?php foreach ( $region_taxonomies as $region_taxonomy ) {
							$get_region = isset($_GET['regions']) ? $_GET['regions'] : '';
							$get_region = explode(',', $get_region);
							?>
							<div class="switch-container">
								<label class="switch"><input type="checkbox" class="choice-filter choice-filter-regions" name="regions[]" value="<?php echo $region_taxonomy->slug; ?>" <?php if (!empty($get_region) && in_array($region_taxonomy->slug, $get_region)){ echo 'checked';} ?>><span
											class="switch-button"></span> <?php echo $region_taxonomy->name; ?></label>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>

			<!-- Location -->
			<div class="sidebar-widget" style="position: relative">
				<h3>Country</h3>
				<?php if (!empty($countries_values) && is_array($countries_values)) {
					sort($countries_values);
					?>
					<select class="selectpicker default choice-filter choice-filter-countries" name="countries[]" multiple data-live-search="true" data-actions-box="true" title="Select Country" >
						<?php foreach ($countries_values as $countries_value) {
							$get_countries = isset($_GET['countries']) ? $_GET['countries'] : '';
							$get_countries = explode(',', $get_countries);
							?>
							<option value="<?php echo $countries_value; ?>" <?php if (!empty($get_countries) && in_array($countries_value, $get_countries)){echo 'selected';} ?>><?php echo $countries_value; ?></option>
						<?php } ?>
					</select>
					<button class="filterBtn" data-parent="countries" data-touch="1">Apply</button>
				<?php } ?>
			</div>

			<!-- City -->
			<div class="sidebar-widget" style="position: relative">
				<h3>City</h3>
				<?php if (!empty($cities_values) && is_array($cities_values)) {
					sort($cities_values);
					?>
					<select class="selectpicker default choice-filter choice-filter-cities" name="cities[]" multiple data-live-search="true" data-actions-box="true" title="Select City" >
						<?php foreach ($cities_values as $cities_value) {
							$get_cities = isset($_GET['cities']) ? $_GET['cities'] : '';
							$get_cities = explode(',', $get_cities);
							?>
							<option value="<?php echo $cities_value; ?>" <?php if (!empty($get_cities) && in_array($cities_value, $get_cities)){echo 'selected';} ?>><?php echo $cities_value; ?></option>
						<?php } ?>
					</select>
					<button class="filterBtn" data-parent="cities" data-touch="1">Apply</button>
				<?php } ?>
			</div>

			<!-- Job posting date -->

			<div class="sidebar-widget">

				<h3>Jobs posted within last</h3>
				<div class="margin-top-20"></div>

				<div class="input-wrapper" style="display: none">
					<input type="num" name="min_posted" id="min_posted" value="" class="range-filter">
				</div>
				<?php
				$min_post_value = min($posted_values);
				$max_post_value = max($posted_values);
				?>
				<input id="range_posted" class="rangeFilter range-posted" name="range_posted" data-slider-posted="days" type="text" data-slider-min="0" data-slider-max="<?php if (!empty($max_post_value)) echo $max_post_value; ?>" data-slider-step="1" data-slider-value="<?php if(!empty($max_post_value)) echo $max_post_value; ?>" />

			</div>

			<!-- Job closing date -->
			<div class="sidebar-widget">
				<h3>Jobs closing within next</h3>
				<div class="margin-top-20"></div>

				<div class="input-wrapper" style="display: none">
					<input type="num" name="min_closing" id="min_closing" value="" class="range-filter">
				</div>
				<input id="range_closing" class="rangeFilter range-closing" name="range_closing" data-slider-date="days" type="text" data-slider-min="0" data-slider-max="<?php if(!empty($closing_values)) echo max($closing_values); ?>" data-slider-step="1" data-slider-value="<?php if(!empty($closing_values)) echo max($closing_values); ?>" />

			</div>

		</div>


		<div class="footer_buttons mobi_only">
        	
			<a href="#" class="show_filter"><?php _e('Show results', 'hireo-theme'); ?></a>
		</div><div class="clearfixa"></div><br>
	</div>
</div>