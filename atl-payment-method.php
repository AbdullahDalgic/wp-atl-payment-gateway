<?php

/**
 * Plugin Name: ATL Payment Gateway for WooCommerce
 * Plugin URI:  https://wordpress.org/plugins/atl-payment-gateway
 * Description: Adds the ATL Payment Gateway to WooCommerce.
 * Version:     1.0.0
 * Author:      Abdullah Dalgıç
 * Text Domain: atl-payment-gateway
 * Author URI:  https://abdullahdalgic.com.tr
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (! defined('ABSPATH')) {
  exit; // Do not allow direct access.
}

add_filter('woocommerce_payment_gateways', 'add_atl_payment_gateway');
function add_atl_payment_gateway($methods)
{
  $methods[] = 'WC_ATL_Payment_Gateway';
  return $methods;
}

add_action('plugins_loaded', 'atl_payment_gateway_init');
function atl_payment_gateway_init()
{
  require 'payment.class.php';
}
