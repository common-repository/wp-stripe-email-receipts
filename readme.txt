=== Plugin Name ===
Contributors: philip.newcomer
Donate link: http://philipnewcomer.net/donate/
Tags: stripe, wp_stripe, wp-stripe, email, receipt, receipts
Requires at least: 3.1
Tested up to: 3.5.2
Stable tag: 1.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add-on for the WP Stripe plugin: Sends an email receipt to the user after a successful Stripe transaction.

== Description ==

*WP Stripe Email Receipts* is an add-on for the [WP Stripe](http://wordpress.org/plugins/wp-stripe/) plugin, which sends an email receipt to the user after a successful Stripe transaction.

It provides a settings page where you can customize the "from" name, "from" email address, message subject, and message body text of the email that is sent to the user.

== Installation ==

*WP Stripe Email Receipts* is installed like any other WordPress plugin:

1. Go to Plugins > Add New in your WordPress dashboard
2. Search for *WP Stripe Email Receipts*
3. Click on "Install Now" under the entry for *WP Stripe Email Receipts*

*Or:*

1. Download the plugin's ZIP file to your computer
1. Go to Plugins > Add New > Upload in your WordPress dashboard
2. Choose the plugin ZIP file that you downloaded in step 1, and click on "Install Now"

Don't forget to activate the plugin after the installation is complete. After installation and activation, no further configuration is required. However, you can optionally go to Settings > Stripe Email Receipts to customize the email that the user receives after a successful transaction.

== Frequently Asked Questions ==

= What are the system requirements? =

WP Stripe Email Receipts requires the following:

* WordPress 3.1 (or whatever version that WP Stripe requires)
* [WP Stripe](http://wordpress.org/plugins/wp-stripe/) installed
* Your web server needs to be properly configured to send email from PHP

= The email receipts aren't being sent to my users. =

That's not a question.

= Okay, why are my users not getting their email receipts? =

There are a number of reasons why your users may not be receiving the emails. It could be that you forgot to activate the plugin in WordPress. It could also be that your web server is not properly configured to send email from PHP. It could also be that the user did not enter an email address when they made the transaction.

== Screenshots ==

1. The plugin settings page

== Changelog ==

= 1.0 =
* Initial release