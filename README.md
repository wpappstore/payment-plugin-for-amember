WP App Store Payment Plugin for aMember 3
=========================================

**Version 0.1**  
**Developed and tested on aMember 3.1.8PRO**

Payment plugin for [aMember](http://www.amember.com/) (version 3) that handles receiving the WP App Store's sale postback. Upon receiving a postback, the plugin goes through the following flow:

1. Checks API key received against the one saved in aMember configuration
1. Checks that the received product SKU exists in aMember
1. Looks for an existing member with the received email address
1. If no member exists with the received email address, creates a new member account and emails the generated username and password to the received email address
1. Adds payment to aMember
1. Sends the member an email, confirming that their account has been updated

Installation
------------

1. [Download](https://github.com/downloads/wpappstore/payment-plugin-for-amember/wpappstore.zip) a zip of the plugin and unzip it
1. Upload the unzipped *wpappstore* folder to your aMember /plugins/payment/ folder
1. Login to your aMember control panel
1. Go to Utilities > Setup/Configuration > Plugins
1. Check *wpappstore* in the list and save
1. Click the new *WP App Store* menu item in the navigation header
1. In a new browser tab, login to your [WP App Store dashboard](https://wpappstore.com/dashboard/)
1. Copy your *API Key*
1. Switch back to your aMember browser tab
1. Paste the copied API key into the *WP App Store API Key* field
1. Set your *SKU Vendor Prefix* and save
1. Make a note to review the rest of the configuration options later
1. At the very bottom of the configuration options page, copy *Your Postback URL*
1. Switch back to your WP App Store browser tab
1. Go to *Edit Publisher*
1. Paste the copied URL into the *Sale Postback URL* field and submit
1. Once your submission has been reviewed and accepted, you will start receiving sale postbacks to aMember

When adding products to the WP App Store, you will need to specify a SKU for each product. The SKU should be a combination of the *SKU Vendor Prefix* you set in your aMember configuration and the aMember ID of the product. For example, lets say you're adding a theme to the WP App Store. In aMember, the theme's ID is *214*. You've set *ACME-* as your prefix in your aMember configuration. When adding the theme to WP App Store, you should specify *ACME-214* as the SKU. If you've already added themes or plugins to the WP App Store, you will need to update them with the proper SKUs.

Testing
-------

1. Login to your [WP App Store dashboard](https://wpappstore.com/dashboard/)
1. Go to *Sale Postback Test*
1. Scroll to the bottom
1. Make sure the *Postback URL* is correctly set
1. Click the *Send Request* button
1. In a new browser tab, login to your aMember control panel
1. Go to Utilities > Error/Debug Log
1. You should see a message starting with *wpappstore ERROR:*
1. Now you can go back to your WP App Store tab, adjust some of the fields, and try *Send Request* again

Troubleshooting
---------------

### Receiving "wpappstore ERROR: No product with id ..."

It is likely you have not correctly specified the *SKU Vendor Prefix* in your aMember configuration, or you have not set the correct SKU for the theme or plugin in WP App Store. Please review the *Installation* section above for more details about SKUs.

Change Log
----------

* 0.1 (2012-04-14)
 * Initial release.
