<?php

/**
 * @file
 * Class instantiation to include css/js files to page header.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class paypal_donation_e_header.
 */
class paypal_donation_e_header
{

	/**
	 * Store plugin preferences.
	 *
	 * @var mixed|null
	 */
	private $plugPrefs = null;

	/**
	 * Constructor.
	 */
	function __construct()
	{
		// Get plugin preferences.
		$this->plugPrefs = e107::getPlugConfig('paypal_donation')->getPref();
		$this->include_components();
	}

	/**
	 * Include necessary CSS and JS files
	 */
	function include_components()
	{
		e107::css('paypal_donation', 'css/paypal_donation.css');
	}

}


// Class instantiation.
new paypal_donation_e_header;
