<?php
/**
 * WP Session handler
 * Plugin URI: https://github.com/davemedema/wp-session
 * Description: Sessions for WordPress.
 * Version: 0.1.2
 * Author: Dave Medema
 * Author URI: http://gofunkyfresh.com
 * License: WTFPL
 *
 * Copyright 2013 Dave Medema
 * This work is free. You can redistribute it and/or modify it under the
 * terms of the Do What The Fuck You Want To Public License, Version 2,
 * as published by Sam Hocevar. See LICENSE.txt for more details.
 */

if (!defined('ABSPATH')) exit;

class WP_Session {

  public $data = array();

  private $_cookie;
  private $_session_expires;
  private $_session_expiring;
  private $_user_id;

  // Constructor.
  // --------------------------------------------------------------------------

  public function __construct() {
    $this->_cookie = 'wp_session_cookie_' . COOKIEHASH;

    // Activation hook
    register_activation_hook(__FILE__, array($this, 'activate'));

    // Actions
    add_action('wp_cleanup_sessions', array($this, 'cleanup'  )     );
    add_action('shutdown',               array($this, 'save_data'), 999);
  }

  // Public methods.
  // --------------------------------------------------------------------------

  /**
   * Activate.
   *
   * @hook register_activation_hook
   */
  public function activate() {
    // Cron jobs
    wp_clear_scheduled_hook('wp_cleanup_sessions');
    wp_schedule_event(time(), 'twicedaily', 'wp_cleanup_sessions');
  }

  /**
   * Clear data.
   */
  public function clear_data() {
    $this->data = array();
    $this->save_data();
  }

  /**
   * Cleanup.
   *
   * @hook wp_cleanup_sessions
   * @priority 10
   */
  public function cleanup() {
    global $wpdb;

    // Flush cache
    wp_cache_flush();

    // Delete rows
    $wpdb->query($wpdb->prepare("
      DELETE a, b
      FROM {$wpdb->options} a, {$wpdb->options} b
      WHERE a.option_name LIKE '_wp_session_%%'
      AND b.option_name = CONCAT('_wp_session_expires_', SUBSTRING(a.option_name, CHAR_LENGTH('_wp_session_') + 1))
      AND b.option_value < %s
    ", time()));
  }

  /**
   * Get data.
   */
  public function get_data() {
    return get_option('_wp_session_' . $this->_user_id, array());
  }

  /**
   * Save data.
   *
   * @hook shutdown
   * @priority 999
   */
  public function save_data() {
    $session_option         = '_wp_session_' . $this->_user_id;
    $session_expires_option = '_wp_session_expires_' . $this->_user_id;

    if (get_option($session_option) === false) {
      add_option($session_option, $this->data, null, 'no');
      add_option($session_expires_option, $this->_session_expires, null, 'no');
    } else {
      update_option($session_option, $this->data);
    }
  }

  /**
   * Start.
   */
  public function start() {
    if ($cookie = $this->_get_cookie()) {
      $this->_user_id          = $cookie[0];
      $this->_session_expires  = $cookie[1];
      $this->_session_expiring = $cookie[2];

      // Update session if it's close to expiring
      if (time() > $this->_session_expiring) {
        $this->_set_expires();
        update_option('_wp_session_expires_' . $this->_user_id,
          $this->_session_expires);
      }
    } else {
      $this->_set_expires();
      $this->_user_id = $this->_generate_user_id();
    }

    $this->data = $this->get_data();

    $to_hash      = $this->_user_id . $this->_session_expires;
    $cookie_hash  = $this->_hash($to_hash);
    $cookie_value = $this->_user_id . '||' . $this->_session_expires . '||'
      . $this->_session_expiring . '||' . $cookie_hash;

    setcookie($this->_cookie, $cookie_value, $this->_session_expires,
      COOKIEPATH, COOKIE_DOMAIN, false, true);
  }

  // Private methods.
  // --------------------------------------------------------------------------

  /**
   * Generate user ID.
   */
  private function _generate_user_id() {
    return is_user_logged_in()
      ? get_current_user_id()
      : wp_generate_password(32, false);
  }

  /**
   * Get cookie.
   */
  private function _get_cookie() {
    if (empty($_COOKIE[$this->_cookie])) return false;

    list($user_id, $session_expires, $session_expiring, $cookie_hash)
      = explode('||', $_COOKIE[$this->_cookie]);

    $to_hash = $user_id . $session_expires;
    $hash    = $this->_hash($to_hash);

    if ($hash !== $cookie_hash) return false;

    return array($user_id, $session_expires, $session_expiring, $cookie_hash);
  }

  /**
   * Hash.
   */
  private function _hash($to_hash) {
    return hash_hmac('md5', $to_hash, wp_hash($to_hash));
  }

  /**
   * Set expires.
   */
  private function _set_expires() {
    $this->_session_expiring = time() + (60 * 60 * 47); // 47 Hours
    $this->_session_expires  = time() + (60 * 60 * 48); // 48 Hours
  }

}


// API.
// ----------------------------------------------------------------------------


/**
 * wp_get_session
 */
function wp_get_session() {
  global $wp_session;
  return $wp_session->data;
}

/**
 * wp_session_save
 */
function wp_session_save($data = array()) {
  global $wp_session;

  if ($data) $wp_session->data = $data;
  $wp_session->save_data();
}

/**
 * wp_session_start
 */
function wp_session_start() {
  global $wp_session;

  $wp_session = new WP_Session();
  $wp_session->start();
}
