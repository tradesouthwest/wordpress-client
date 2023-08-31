<?php

if ( !defined('ABSPATH') ) exit;

function quickcab_render_quickcab_form_shortcode($options) {
  if ( empty(QuickCab_Vehicle::find_all()) ) {
    return esc_html__('You must create at least 1 vehicle to show the form.', 'quickcab');
  }

  $booking_form = new QuickCab_Booking_Form($options['id']);

  // get i18n adjusted booking form ID
  $form_id = apply_filters('wpml_object_id', $booking_form->get_ID(), 'qc_booking_form', TRUE);
  
  $booking_time_interval = intval(quickcab_get_option('booking_time_interval_minutes'));
  $collection_fixed_addresses = QuickCab_Fixed_Address::find_all_collection();
  $destination_fixed_addresses = QuickCab_Fixed_Address::find_all_destination();

  ob_start();

  ?><form class="quickcab-booking-form quickcab-booking-form1" name="quickcab_booking_form" method="POST" action="" data-form-id="<?php echo $form_id; ?>">
    <input type="hidden" class="quickcab-form-vehicle-id" name="quickcab_form[vehicle_id]">
    <input type="hidden" name="quickcab_form[id]" class="quickcab-form-id" value="<?php echo $form_id; ?>" />

    <input type="hidden" class="quickcab-total" name="quickcab_form[total]">

    <div class="cart-steps quickcab-cart-steps">
      <ul>
        <li class="col-xs-12 col-sm-12 col-md-4 active quickcab-cart-step -section0">
          <a href="#" class="cart-step-link" data-index="0">
            <span class="quickcab-section-number">1</span>
            <div class="quickcab-section-title"><?php
              echo __('Journey Information', 'quickcab');
            ?></div>
          </a>
        </li>
        <li class="col-xs-12 col-sm-12 col-md-4 quickcab-cart-step -section1">
          <a href="#" class="cart-step-link" data-index="1">
            <span class="quickcab-section-number">2</span>
            <div class="quickcab-section-title"><?php
              echo __('Select Vehicle', 'quickcab');
            ?></div>
          </a>
        </li>
        <li class="col-xs-12 col-sm-12 col-md-4 quickcab-cart-step -section2">
          <a href="#" class="cart-step-link" data-index="2">
            <span class="quickcab-section-number">3</span>
            <div class="quickcab-section-title"><?php
              echo __('Payment', 'quickcab');
            ?></div>
          </a>
        </li>
      </ul>
    </div>

    <div class="form-section form-section0">
      <div class="row quickcab-error" style="display: none;">
        <div class="col-md-12">
          <div class="alert alert-danger" role="alert">
            <span class="quickcab-error-message"></span>
            <h5><?php echo __('For further assistance or to request additional buses, please fill out this form: ', 'quickcab' ); ?></h5>
            <p><a href="<?php echo site_url( '/contattaci/', 'https' ); ?>">Modulo di Contatto</a></p>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="quickcab-input-group">
            <div class="row quickcab-header-row">
              <h3 class="quickcab-header"><?php
                echo __('Journey Information', 'quickcab');
              ?></h3>
            </div>
            <div class="quickcab-input-row">
              <label for="quickcab_form_starting_address_<?php echo $form_id; ?>" class="control-label"><?php
                echo __('Collection Address', 'quickcab');
                ?> <span class="quickcab-required">*</span>
              </label><?php

              if ( $booking_form->get_fixed_address_collection() ) {
                ?><select name="quickcab_form[origin][address]" class="form-control quickcab-form-input quickcab-select-input quickcab-form-starting-address<?php if ($booking_form->enable_waypoints()) {?> quickcab-waypoints-active<?php } ?>" id="quickcab_form_starting_address_<?php echo $form_id; ?>">
                  <option value="" disabled <?php if (count($collection_fixed_addresses) != 1) { ?>selected<?php } ?>><?php
                    echo esc_html__('Select a collection address', 'quickcab') . '...';
                  ?></option><?php

                  foreach ( $collection_fixed_addresses as $collection_fixed_address ) {
                    ?><option value="<?php echo esc_attr($collection_fixed_address->get_address()); ?>" <?php if (count($collection_fixed_addresses) == 1) { ?>selected<?php } ?>><?php
                      echo esc_html($collection_fixed_address->get_name());
                    ?></option><?php
                  }
                ?></select><?php

                if ( $booking_form->enable_waypoints() ) {
                  ?><div class="quickcab-waypoint-actions-container">
                    <a href="#" class="quickcab-add-waypoint" data-insert-at="0">+</a>
                  </div><?php
                }
              } else {
                $origin_input_additional_attributes = apply_filters('quickcab_origin_input_additional_attributes', '');
                ?><input type="text" class="form-control quickcab-form-input quickcab-form-starting-address<?php if ($booking_form->enable_waypoints()) { ?> quickcab-waypoints-active<?php } ?>" id="quickcab_form_starting_address_<?php echo $form_id; ?>" autocomplete="off" <?php echo $origin_input_additional_attributes; ?>>
                <input type="hidden" class="quickcab-hidden-autocomplete-field" name="quickcab_form[origin][address]" autocomplete="off"><?php

                if ( $booking_form->enable_waypoints() ) {
                  ?><div class="quickcab-waypoint-actions-container">
                    <a href="#" class="quickcab-add-waypoint" data-insert-at="0">+</a>
                  </div><?php
                }
              }
              ?><input type="hidden" class="quickcab-origin-coords-lat" name="quickcab_form[origin][lat]">
              <input type="hidden" class="quickcab-origin-coords-lng" name="quickcab_form[origin][lng]">

              <div class="quickcab-autocomplete-warning qc_form_warning_quickcab_form_starting_address_<?php echo $form_id; ?>" style="display: none;"><?php
                echo __('You must select an option from the drop-down that appears when you type.', 'quickab');
              ?></div>
            </div><?php

            if ( $booking_form->enable_waypoints() ) {
              ?><div class="quickcab-waypoint-container"></div><?php
            }

            ?><div class="row quickcab-input-row">
              <label for="quickcab_form_destination_address_<?php echo $form_id; ?>" class="control-label"><?php
                echo __('Destination Address', 'quickcab');
                ?> <span class="quickcab-required">*</span>
              </label><?php

              if ( $booking_form->get_fixed_address_destination()) {
                ?><select name="quickcab_form[destination][address]" id="quickcab_form_destination_address_<?php echo $form_id; ?>" class="quickcab-select-input form-control quickcab-form-input quickcab-form-destination-address">
                  <option value="" disabled <?php if (count($destination_fixed_addresses) != 1) { ?>selected<?php } ?>><?php
                    echo esc_html__('Select a destination address', 'quickcab') . '...';
                  ?></option><?php

                  foreach ( $destination_fixed_addresses as $destination_fixed_address ) {
                    ?><option value="<?php echo esc_attr($destination_fixed_address->get_address()); ?>" <?php if (count($destination_fixed_addresses) == 1) { ?>selected<?php } ?>><?php
                      echo esc_html($destination_fixed_address->get_name());
                    ?></option><?php
                  }
                ?></select><?php
              } else {
                $destination_input_additional_attributes = apply_filters('quickcab_destination_input_additional_attributes', '');
                ?><input type="text" class="form-control quickcab-form-input quickcab-form-destination-address" id="quickcab_form_destination_address_<?php echo $form_id; ?>" autocomplete="off" <?php echo $destination_input_additional_attributes; ?>>
                <input type="hidden" class="quickcab-hidden-autocomplete-field" name="quickcab_form[destination][address]" autocomplete="off"><?php
              }
              ?><input type="hidden" class="quickcab-destination-coords-lat" name="quickcab_form[destination][lat]">
              <input type="hidden" class="quickcab-destination-coords-long" name="quickcab_form[destination][lng]">

              <div class="quickcab-autocomplete-warning qc_form_warning_quickcab_form_destination_address_<?php echo $form_id; ?>" style="display: none;"><?php
                echo __('You must select an option from the drop-down that appears when you type.', 'quickab');
              ?></div>
            </div>
          </div>

          <div class="quickcab-input-group">
            <div class="quickcab-header-row">
              <h3 class="quickcab-header"><?php
                echo __('Options', 'quickcab');
              ?></h3>
            </div><?php

            if ( $booking_form->enable_direction_field() ) {
              ?><div class="quickcab-input-row">
                <label for="quickcab_form_direction_<?php echo $form_id; ?>" class="control-label"><?php
                  echo __('Direction', 'quickcab');
                ?> <span class="quickcab-required">*</span></label>

                <select name="quickcab_form[direction]" id="quickcab_form_direction_<?php echo $form_id; ?>" class="quickcab-select-input form-control quickcab-form-input quickcab-form-direction">
                  <option value="one-way" selected><?php
                    echo esc_html__('One Way', 'quickcab');
                  ?></option>
                  <option value="return"><?php
                    echo esc_html__('Return', 'quickcab');
                  ?></option>
                </select>
              </div><?php
            }

            if ( $booking_form->enable_passengers_field() || $booking_form->enable_suitcases_field() ) {
              ?><div class="row quickcab-input-row"><?php
                if ( $booking_form->enable_passengers_field() ) {
                  ?><div class="col-xs-12 col-sm-12 col-md-<?php if ($booking_form->enable_suitcases_field()) { ?>6<?php } else { ?>12<?php } ?>">
                    <label for="quickcab_form_occupants_<?php echo $form_id; ?>" class="control-label"><?php
                      echo __('Passengers', 'quickcab');
                    ?> <span class="quickcab-required">*</span></label>

                    <select name="quickcab_form[occupants]" id="quickcab_form_occupants_<?php echo $form_id; ?>" class="quickcab-select-input form-control quickcab-form-input quickcab-form-occupants"><?php
                      $max_occupants = intval(get_option('quickcab_max_occupant_space', 4));

                      for ( $i = 1; $i <= $max_occupants; $i++ ) {
                        ?><option value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></option><?php
                      }
                    ?></select>
                  </div><?php
                }

                if ( $booking_form->enable_suitcases_field() ) {
                  ?><div class="col-xs-12 col-sm-12 col-md-<?php if ($booking_form->enable_passengers_field()) { ?>6<?php } else { ?>12<?php } ?>">
                    <label for="quickcab_form_suitcases_<?php echo $form_id; ?>" class="control-label"><?php
                      echo esc_html__('Passeggeri minorenni', 'quickcab');
                    ?></label>

                    <select name="quickcab_form[suitcases]" id="quickcab_form_suitcases_<?php echo $form_id; ?>" class="quickcab-select-input form-control quickcab-form-input quickcab-form-suitcases"><?php
                      //$max_suitcases = intval(get_option('quickcab_max_suitcase_space', 4));
                      $max_occupants = intval(get_option('quickcab_max_occupant_space', 4));
                      for ( $i = 0; $i <= $max_occupants; $i++ ) {
                        ?><option value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></option><?php
                      }
                    ?></select>
                  </div><?php
                }
              ?></div><?php
            }

            ?><div class="quickcab-input-row">
              <label for="quickcab_form_departure_date_<?php echo $form_id; ?>" class="control-label"><?php
                echo __('Departure Date', 'quickcab');
              ?> <span class="quickcab-required">*</span></label>
              <input type="text" name="quickcab_form[departure_date]" class="form-control quickcab-form-input quickcab-form-departure-date" id="quickcab_form_departure_date_<?php echo $form_id; ?>" placeholder="<?php echo esc_html__('Date', 'quickcab'); ?>" autocomplete="off">
            </div>

            <div class="quickcab-input-row">
              <label for="quickcab_form_departure_time_<?php echo $form_id; ?>" class="control-label"><?php
                echo __('Departure Time', 'quickcab');
              ?> <span class="quickcab-required">*</span></label>

              <select name="quickcab_form[departure_time]" id="quickcab_form_departure_time_<?php echo $form_id; ?>" class="quickcab-select-input form-control quickcab-form-input quickcab-form-departure-time" required>
                <option disabled selected><?php echo esc_html__('Time', 'quickcab'); ?></option><?php
                for ( $h = 0; $h <= 23; $h++ ) {
                  for ( $m = 0; $m < 60; $m += $booking_time_interval ) {
                    ?><option value="<?php echo sprintf('%02d', $h); ?>:<?php echo sprintf('%02d', $m); ?>"><?php
                      echo sprintf('%02d', $h) . ':' . sprintf('%02d', $m);
                    ?></option><?php
                  }
                }
              ?></select>
            </div>

            <div class="quickcab-input-row quickcab-return-journey-options" style="display: none;">
              <label for="quickcab_form_return_departure_date_<?php echo $form_id; ?>" class="control-label"><?php
                echo __('Return Departure Date', 'quickcab');
              ?> <span class="quickcab-required">*</span></label>
              <input type="text" name="quickcab_form[return_departure_date]" class="form-control quickcab-form-input quickcab-form-return-departure-date" id="quickcab_form_return_departure_date_<?php echo $form_id; ?>" placeholder="<?php echo esc_html__('Date', 'quickcab'); ?>">
            </div>

            <div class="quickcab-input-row quickcab-return-journey-options" style="display: none;">
              <label for="quickcab_form_return_departure_time_<?php echo $form_id; ?>" class="control-label"><?php
                echo __('Return Departure Time', 'quickcab');
              ?> <span class="quickcab-required">*</span></label>

              <select name="quickcab_form[return_departure_time]" id="quickcab_form_return_departure_time_<?php echo $form_id; ?>" class="quickcab-select-input form-control quickcab-form-input quickcab-form-return-departure-time">
                <option disabled selected><?php echo esc_html__('Time', 'quickcab'); ?></option><?php
                for ( $h = 0; $h <= 23; $h++ ) {
                  for ( $m = 0; $m < 60; $m += $booking_time_interval ) {
                    ?><option value="<?php echo sprintf('%02d', $h); ?>:<?php echo sprintf('%02d', $m); ?>"><?php
                      echo sprintf('%02d', $h) . ':' . sprintf('%02d', $m);
                    ?></option><?php
                  }
                }
              ?></select>
            </div><?php

            if ( $booking_form->enable_extra_waiting_time() ) {
              ?><div class="quickcab-input-row">
                <label for="quickcab_form_waiting_time_<?php echo $form_id; ?>" class="control-label"><?php
                  echo __('Extra Waiting Time', 'quickcab') . ' (' . $booking_form->get_extra_waiting_time_unit_i18n() . ')';
                ?></label>

                <select name="quickcab_form[extra_waiting_time]" id="quickcab_form_waiting_time_<?php echo $form_id; ?>" class="quickcab-select-input form-control quickcab-form-input quickcab-form-waiting-time"><?php
                  if ( $booking_form->get_extra_waiting_time_unit() == 'hours' ) {
                    for ( $i = 0; $i <= $booking_form->get_extra_waiting_time_max_value(); $i++ ) {
                      ?><option value="<?php echo $i * 60; ?>"><?php
                        echo $i . ' ' . ($i == 1 ? esc_html__('hour', 'quickcab') : esc_html__('hours', 'quickcab'));
                      ?></option><?php
                    }
                  } else {
                    for ( $i = 0; $i <= $booking_form->get_extra_waiting_time_max_value(); $i += 5 ) {
                      ?><option value="<?php echo $i; ?>"><?php
                        echo $i . ' ' . esc_html__('minutes', 'quickcab');
                      ?></option><?php
                    }
                  }
                ?></select>
              </div><?php
            }
          ?></div>
        </div>

        <div class="col-md-6">
          <div class="quickcab-header-row">
            <h3 class="quickcab-header"><?php
              echo __('Map', 'quickcab');
            ?></h3>
          </div>

          <div class="quickcab-form-map" data-form-id="<?php echo $form_id; ?>"><?php
            echo __('Loading map', 'quickcab') . '...';
          ?></div><?php

          if ( $booking_form->show_distance() || $booking_form->show_duration() ) {
            ?><div class="quickcab-journey-stats">
              <div class="row"><?php
                if ( $booking_form->show_distance() ) {
                  ?><div class="col-md-<?php if ($booking_form->show_duration()) { ?>6<?php } else { ?>12<?php } ?> quickcab-map-stats">
                    <div class="quickcab-map-stats-details">
                      <div class="quickcab-map-stat-title"><?php
                        echo __('Distance', 'quickcab');
                      ?></div>
                      <div class="quickcab-map-stat-text -distance">
                        &mdash;
                      </div>
                    </div>
                  </div><?php
                }

                if ( $booking_form->show_duration() ) {
                  ?><div class="col-md-<?php if ($booking_form->show_distance()) { ?>6<?php } else { ?>12<?php } ?> quickcab-map-stats">
                    <div class="quickcab-map-stats-details">
                      <div class="quickcab-map-stat-title"><?php
                        echo __('Journey Time', 'quickcab');
                      ?></div>
                      <div class="quickcab-map-stat-text -duration">
                        &mdash;
                      </div>
                    </div>
                  </div><?php
                }
              ?></div>
            </div><?php
          }
        ?></div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <a href="#" class="quickcab-button pull-right quickcab-booking-form1-section1-submit" data-disable-with="<?php echo esc_html__('Finding vehicles', 'quickcab') . '...'; ?>" data-original-text="<?php echo esc_html__('Select vehicle', 'quickcab'); ?>"><?php
            echo __('Select vehicle', 'quickcab');
          ?></a>
        </div>
      </div>
    </div>

    <div class="form-section form-section1" style="display: none;">
      <div class="row">
        <div class="col-md-3">
          <div class="quickcab-trip-preview">
            <h4 class="quickcab-trip-preview-header"><?php
              echo __('Journey Information', 'quickcab');
            ?></h4>

            <div class="quickcab-trip-preview-title"><?php
              echo __('Route', 'quickcab');
            ?></div>
            <div class="quickcab-trip-preview-text -route"></div>

            <div class="quickcab-trip-preview-title"><?php
              echo __('Collection Time', 'quickcab');
            ?></div>
            <div class="quickcab-trip-preview-text -collection-time"></div>

            <div class="quickcab-trip-preview-title  -return-collection-time" style="display: none;"><?php
              echo __('Return Collection Time', 'quickcab');
            ?></div>
            <div class="quickcab-trip-preview-text -return-collection-time" style="display: none;"></div><?php
            
            if ( $booking_form->show_distance() ) {
              ?><div class="quickcab-trip-preview-title"><?php
                echo __('Distance', 'quickcab');
              ?></div>
              <div class="quickcab-trip-preview-text -distance"></div><?php
            }

            if ( $booking_form->show_duration() ) {
              ?><div class="quickcab-trip-preview-title"><?php
                echo __('Duration', 'quickcab');
              ?></div>
              <div class="quickcab-trip-preview-text -duration"></div><?php
            }
          ?></div>

          <div class="text-center quickcab-back-button-container">
            <a href="#" class="quickcab-button -inverted quickcab-form-back-button"><?php
              echo '< ' . __('Back', 'quickcab');
            ?></a>
          </div>
        </div>

        <div class="col-md-9">
          <div class="quickcab-vehicles-list"></div>
        </div>
      </div>
    </div>
  </form><?php

  $output_string = ob_get_contents();
  ob_end_clean();

  return $output_string;
}
