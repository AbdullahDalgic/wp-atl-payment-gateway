<?php

// Prevent direct access to the file for security reasons
if (! defined('ABSPATH')) {
  exit;
}

class WC_ATL_Payment_Gateway extends WC_Payment_Gateway
{
  public function __construct()
  {
    $this->id                 = 'atl_payment';
    $this->method_title       = __('ATL Payment', 'atl-payment');
    $this->method_description = __('A custom payment gateway for ATL Payment. <a target="_blank" href="https://doc.atlpay.com/docs/category/getting-started">Atl Payment Documentation</a>', 'atl-payment');
    $this->supports           = array('products');

    // Initialize settings
    $this->init_form_fields();
    $this->init_settings();

    // Get values from settings
    $this->enabled     = $this->get_option('enabled');
    $this->title       = $this->get_option('title');
    $this->description = $this->get_option('description');

    // Process payments and handle callbacks
    add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
    add_action('woocommerce_api_atl_callback', array($this, 'check_response'));

    // Save settings
    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
  }

  // Define the settings form fields
  public function init_form_fields()
  {
    $this->form_fields = array(
      'enabled'      => array(
        'title'   => __('Enable/Disable', 'atl-payment'),
        'type'    => 'checkbox',
        'label'   => __('Enable ATL Payment', 'atl-payment'),
        'default' => 'yes',
      ),
      'debug' => array(
        'title'       => __('Debug Mode', 'atl-payment'),
        'type'        => 'checkbox',
        'label'       => __('Enable logging', 'atl-payment'),
        'default'     => 'no',
      ),
      'title'        => array(
        'title'       => __('Title', 'atl-payment'),
        'type'        => 'text',
        'description' => __('The title displayed to the customer at checkout.', 'atl-payment'),
        'default'     => __('ATL Payment', 'atl-payment'),
        'desc_tip'    => true,
      ),
      'description'  => array(
        'title'       => __('Description', 'atl-payment'),
        'type'        => 'textarea',
        'description' => __('The description displayed to the customer at checkout.', 'atl-payment'),
        'default'     => __('Pay securely using ATL Payment.', 'atl-payment'),
      ),
      'supported_currencies' => array(
        'title'       => __('Supported currencies', 'atl-payment'),
        'type'        => 'text',
        'description' => __('You can write supported currencies separated by commas. Default: GBP, EUR, USD', 'atl-payment'),
        'default'     => "GBP, EUR, USD",
      ),
      'api_url'      => array(
        'title'       => __('API URL', 'atl-payment'),
        'type'        => 'text',
        'description' => __('The API URL to send payment requests.', 'atl-payment'),
        'default'     => 'https://service.atlpay.com/merchant/transaction/init',
        'desc_tip'    => true,
      ),
      'api_key'      => array(
        'title'       => __('API Key', 'atl-payment'),
        'type'        => 'text',
        'description' => __('The API key to authenticate the payment requests.', 'atl-payment'),
        'default'     => '',
        'desc_tip'    => true,
      ),
      'callback_url' => array(
        'title'       => __('Callback URL', 'atl-payment'),
        'type'        => 'textarea',
        'description' => __('The URL for the payment gateway to call after the payment is processed.<br /> Default:', 'atl-payment') . ' ' . site_url("/checkout/order-received/{order_id}/"),
        'default'     => site_url("/checkout/order-received/{order_id}/"),
      ),
      'success_redirect' => array(
        'title'       => __('Successful payment redirect page', 'atl-payment'),
        'type'        => 'text',
        'description' => __('After the callback is returned, if the operation is successful, redirect to this page.', 'atl-payment'),
        'default'     => "/",
      ),
      'failed_redirect' => array(
        'title'       => __('Failed payment redirect page', 'atl-payment'),
        'type'        => 'text',
        'description' => __('After the callback is returned, if the operation is failed, redirect to this page.', 'atl-payment'),
        'default'     => "/",
      ),
    );
  }

  // Process the payment request
  public function process_payment($order_id)
  {
    $order = wc_get_order($order_id);

    // Retrieve API URL from settings
    $api_url = $this->get_option('api_url');

    $redirectionUrl = $this->get_option('callback_url');

    // Replace placeholders in callback URL with actual order details
    $redirectionUrl = str_replace('{order_id}', $order_id, $redirectionUrl);
    $redirectionUrl = str_replace('{order_key}', $order->get_order_key(), $redirectionUrl);

    // Prepare request body
    $body = [
      "amount" => $order->get_total() . "00",
      "key" => $this->get_option('api_key'),
      "paymentCurrencyAlpha3Code" => $order->get_currency(),
      "redirectionUrl" => $redirectionUrl,
      "paymentInstrumentType" => "CARD",
      "billingCountryAlpha2Code" => $order->get_billing_country(),
      "extraInfo" => [
        "firstName" => $order->get_billing_first_name(),
        "lastName" => $order->get_billing_last_name(),
        "emailId" => $order->get_billing_email(),
        "customerUniqueId" => $order->get_billing_email(),
        "city" => $order->get_billing_city(),
        "zip" => strlen($order->get_billing_postcode()) > 0 ? $order->get_billing_postcode() : '00000',
        "address1" => $order->get_billing_address_1(),
        "address2" => strlen($order->get_billing_address_2()) > 0 ? $order->get_billing_address_2() : $order->get_billing_address_1(),
        "billingAddress" => [
          "firstName" => $order->get_billing_first_name(),
          "lastName" => $order->get_billing_last_name(),
          "email" => $order->get_billing_email(),
          "phone" => $order->get_billing_phone(),
          "city" => $order->get_billing_city(),
          "address" => $order->get_billing_address_1(),
          "zip" => strlen($order->get_billing_postcode()) > 0 ? $order->get_billing_postcode() : '00000',
          "state" => $order->get_billing_state(),
          "country" => $order->get_billing_country()
        ],
        "shippingAddress" => [
          "firstName" => $order->get_billing_first_name(),
          "lastName" => $order->get_billing_last_name(),
          "email" => $order->get_billing_email(),
          "phone" => $order->get_billing_phone(),
          "city" => $order->get_billing_city(),
          "address" => $order->get_billing_address_1(),
          "zip" => strlen($order->get_billing_postcode()) > 0 ? $order->get_billing_postcode() : '00000',
          "state" => $order->get_billing_state(),
          "country" => $order->get_billing_country()
        ],
        "phoneNumber" => $order->get_billing_phone(),
        "state" => $order->get_billing_state(),
        "country" => $order->get_billing_country(),
      ]
    ];
    if ('yes' === $this->get_option('debug')) {
      wc_get_logger()->debug('ATL Payment request body.', array('source' => 'atl-payment', 'body' => $body));
    }

    // Send API request
    $response = wp_remote_post($api_url, array(
      'method'    => 'POST',
      'body'      => json_encode($body),
      'headers'   => array(
        'Content-Type' => 'application/json',
      ),
    ));

    $result = json_decode(wp_remote_retrieve_body($response), true);

    if ('yes' === $this->get_option('debug')) {
      wc_get_logger()->debug('ATL Payment request sent.', array('source' => 'atl-payment', $result['message']));
    }

    if (is_wp_error($response)) {
      wc_add_notice("<b>" . __('Payment error:', 'atl-payment') . "</b> " . $response->get_error_message(), 'error');
      return;
    }

    // Handle the response and redirect the user
    if (isset($result['redirectionUrl'])) {
      return array(
        'result'   => 'success',
        'redirect' => $result['redirectionUrl'],
      );
    }

    wc_add_notice("<b>" . __('Payment error:', 'atl-payment') . "</b> " . $result['message'], 'error');
    return;
  }

  // Handle the payment gateway callback
  public function check_response()
  {
    // Debug log
    if ('yes' === get_option('atl_payment_debug')) {
      wc_get_logger()->debug('ATL Payment callback received.', array('source' => 'atl-payment'));
    }

    // Retrieve callback parameters
    $transaction_code = isset($_GET['transactionCode']) ? sanitize_text_field($_GET['transactionCode']) : '';
    $success = isset($_GET['success']) ? filter_var($_GET['success'], FILTER_VALIDATE_BOOLEAN) : false;
    $order_id = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;


    if (! $order_id || ! $transaction_code) {
      if ('yes' === get_option('atl_payment_debug')) {
        wc_get_logger()->debug('ATL Payment callback error: Missing order ID or transaction code.', array('source' => 'atl-payment'));
      }
      wp_die('Invalid callback request.', 'ATL Payment', array('response' => 400));
    }

    // Load the order
    $order = wc_get_order($order_id);
    if (! $order) {
      if ('yes' === get_option('atl_payment_debug')) {
        wc_get_logger()->debug("ATL Payment callback error: Order #{$order_id} not found.", array('source' => 'atl-payment'));
      }
      wp_die('Order not found.', 'ATL Payment', array('response' => 404));
    }


    // Debug log
    if ('yes' === get_option('atl_payment_debug')) {
      wc_get_logger()->debug("ATL Payment callback processed for order #{$order_id}. Success: " . ($success ? 'Yes' : 'No'), array('source' => 'atl-payment'));
    }

    // Update order status based on payment success or failure
    if ($success) {
      $order->add_order_note(sprintf(__('ATL Payment successful. Transaction Code: %s', 'atl-payment'), $transaction_code));
      $order->payment_complete();

      WC()->cart->empty_cart();


      wp_redirect(get_option('success_redirect'));
      exit;
    } else {
      $order->add_order_note(sprintf(__('ATL Payment failed. Transaction Code: %s', 'atl-payment'), $transaction_code));
      $order->update_status('failed', __('Payment failed via ATL Payment.', 'atl-payment'));

      wp_redirect(get_option('failed_redirect'));
      exit;
    }


    wp_die('Callback processed successfully.', 'ATL Payment', array('response' => 200));
  }

  // Determine if the payment method is available
  public function is_available()
  {
    if ('yes' === $this->get_option('debug')) {
      wc_get_logger()->debug('ATL Payment is_available called.', array('source' => 'atl-payment'));
    }

    if ('yes' !== $this->get_option('enabled')) {
      return false;
    }

    if (! is_checkout()) {
      return false;
    }

    // Get supported currencies from settings
    $currencies = [];
    foreach (explode(",", $this->get_option("supported_currencies")) as $key => $value) {
      $currencies[$key] = strtoupper(trim($value));
    }

    if (! in_array(get_woocommerce_currency(), $currencies)) {
      if ('yes' === $this->get_option('debug')) {
        wc_get_logger()->debug('ATL Payment unavailable due to unsupported currency.', array('source' => 'atl-payment'));
      }
      return false;
    }

    return true;
  }
}
