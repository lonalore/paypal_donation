PayPal Donation (e107 plugin)
=============================

[![Join the chat at https://gitter.im/lonalore/paypal_donation](https://badges.gitter.im/lonalore/paypal_donation.svg)](https://gitter.im/lonalore/paypal_donation?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Requirements:
- e107 CMS v2
- PayPal account

> Planned features:
> - Ability to create unlimited donation-campaigns (menu items) **[DONE]**
> - Ability to define goals (goal amount and date) **[DONE]**
> - Ability to add pre-defined amounts and/or "custom amount" field for donation forms **[DONE]**
> - Access management for menu items **[DONE]**
> - Track Donations (and Donors), store PayPal IPN messages **[DONE]**
> - Goal tracking (per menu item) **[DONE]**
> - Reporting features to Admin UI (lists, charts, exports, etc)
> - Email donors customized email Thank You message.
> - Implements e_notify.php
> - Assign userclass to donor (user) automatically
> - NodeJS integration: real-time menu-items updates with push-notifications

### Installation and configuration

- Create your Paypal Business Account
- Install PayPal Donation plugin
- Configure PayPal Donation plugin
    - Goto PayPal Donation settings page, and set your Paypal (live and sandbox) email address.
    - Create a donation campaign
    - Create amounts for your donation campaign
    - Place your PayPal Donation menu to where you want to display it

### How to test your paypal integration with sandbox

In order to test your paypal integration with sandbox follow these steps:

- **Step 1.** This plugin is using **Classic API** read more [here](https://developer.paypal.com/webapps/developer/docs/classic/).
- **Step 2.** Register with **paypal.com** and get verified but getting verified takes some time so you should already be registered long before starting testing.
- **Step 3.** Use your credentials from above to login to **developer.paypal.com**.
- **Step 4.** Go to Dashboard -> Accounts (under sandbox heading).
- **Step 5.** Create at least one USER and one BUSINESS (MERCHANT) sandbox accounts. Emails don't have to real because mails are never send.
- **Step 6.** Setup PayPal Donation plugin to use the sandbox using the sandbox merchant email you created on Step 5.
- **Step 7.** Login to **sandbox.paypal.com** as USER or MERCHANT to see your balances.
- **Step 8.** Setup a donation campaign (See **Installation and configuration**).
- **Step 9.** Test donating using the sandbox USER - you should be redirected to the **sandbox.paypal.com** site and complete the purchase.
- **Step 10.** The MERCHANT user might have to login to **sandbox.paypal.com** and accept the payment (if its not setup in the settings).
- **Step 11.** Now you should see the payment in PayPal Donation menu you placed to your website.

### Questions about this project?

Please feel free to report any bug found. Pull requests, issues, and plugin recommendations are more than welcome!

### Donate with [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PQYDBAMQ3D2UG)

If you think this plugin is useful and saves you a lot of work, a lot of costs (PHP developers are expensive) and let you sleep much better, then donating a small amount would be very cool.

[![Paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PQYDBAMQ3D2UG)

Screenshots
===========

### Front-end - Menu item
![Screenshot 1](https://www.dropbox.com/s/ztihu1r4g83i33d/01.png?dl=1)

### Admin Area
![Screenshot 2](https://www.dropbox.com/s/m8ummaw8ah8mma1/02.png?dl=1)
![Screenshot 3](https://www.dropbox.com/s/ni8skhnnog8mhub/03.png?dl=1)
![Screenshot 4](https://www.dropbox.com/s/2tcm7ezwvtng4kc/04.png?dl=1)

## Support on Beerpay
Hey dude! Help me out for a couple of :beers:!

[![Beerpay](https://beerpay.io/lonalore/paypal_donation/badge.svg?style=beer-square)](https://beerpay.io/lonalore/paypal_donation)  [![Beerpay](https://beerpay.io/lonalore/paypal_donation/make-wish.svg?style=flat-square)](https://beerpay.io/lonalore/paypal_donation?focus=wish)
