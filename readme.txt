=== ATL Payment Gateway for WooCommerce ===
Contributors: abdullahdalgic
Donate link: https://abdullahdalgic.com.tr
Tags: woocommerce, payment gateway, payment method, atl, custom payment
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds ATL Payment Gateway to WooCommerce. A custom payment gateway that integrates ATL's secure payment API.

== Description ==
ATL Payment Gateway for WooCommerce is a custom payment gateway that integrates ATL's secure API to allow merchants to process payments directly through their WooCommerce stores.

**Features:**
- Customizable settings for API Key, API URL, Callback URL, and Success/Failed redirect URLs.
- Supports multiple currencies: GBP, EUR, USD, TRY.
- Logs payment activities with WooCommerce's debug logger.
- Redirects users based on successful or failed payment transactions.
- Compatible with the latest WooCommerce versions.

This plugin is perfect for merchants who want to use ATL's secure payment API as a payment gateway in WooCommerce.

**Requirements:**
- WooCommerce installed and activated.
- PHP 7.4 or higher.

For documentation, visit the [ATL Payment Documentation](https://doc.atlpay.com/docs/category/getting-started).

== Installation ==
1. Upload the `atl-payment-gateway` folder to the `/wp-content/plugins/` directory or install via the WordPress admin area.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **WooCommerce > Settings > Payments** and enable `ATL Payment`.
4. Configure the API Key, API URL, Callback URL, and other settings.

== Screenshots ==
1. **ATL Payment Gateway Settings**  
   ![Screenshot 1](https://raw.githubusercontent.com/AbdullahDalgic/wp-atl-payment-gateway/refs/heads/master/assets/screenshots/screenshot1.jpg)
   Configure your ATL Payment settings, including API Key, Callback URL, and redirection settings.

2. **Checkout with ATL Payment Gateway**  
   ![Screenshot 2](https://raw.githubusercontent.com/AbdullahDalgic/wp-atl-payment-gateway/refs/heads/master/assets/screenshots/screenshot2.jpg)
   Customers can select ATL Payment Gateway during checkout.

3. **Order Details with Transaction Code**  
   ![Screenshot 3](https://raw.githubusercontent.com/AbdullahDalgic/wp-atl-payment-gateway/refs/heads/master/assets/screenshots/screenshot3.jpg)
   View transaction details in the WooCommerce order notes.

== Frequently Asked Questions ==

= How do I obtain my API Key? =
You can obtain your API Key by signing up at [ATL Payment](https://doc.atlpay.com).

= Does this plugin support multiple currencies? =
Yes, the plugin supports GBP, EUR, USD, and TRY by default. Additional currencies can be added in the settings.

= Is this plugin secure? =
Yes, all payment requests are securely sent via the ATL Payment API. No sensitive data is stored on your server.

= What happens if a payment fails? =
If a payment fails, the customer is redirected to the Failed Redirect page defined in the plugin settings.

== Changelog ==

= 1.0.0 =
* Initial release of ATL Payment Gateway for WooCommerce.
* Added support for multiple currencies (GBP, EUR, USD).
* Integrated customizable API Key, Callback URL, and redirection settings.
* Added WooCommerce logging for debug mode.
* Supports WooCommerce's order status updates for successful and failed payments.

== Upgrade Notice ==
= 1.0.0 =
Initial release. Install to add ATL Payment support to WooCommerce.