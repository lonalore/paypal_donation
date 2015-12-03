<?php

/**
 * @file
 * IPN listener to receive PayPal IPN messages.
 */

if(!defined('e107_INIT'))
{
	require_once('../../class2.php');
}

if(!e107::isInstalled('paypal_donation'))
{
	header('Location: ' . e_BASE . 'index.php');
	exit;
}

// [PLUGINS]/paypal_donation/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('paypal_donation', false, true);


/**
 * Class ipn_listener.
 */
class ipn_listener
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
	public function __construct()
	{
		// Get plugin preferences.
		$this->plugPrefs = e107::getPlugConfig('paypal_donation')->getPref();

		$ipn = $this->readIPN();
		$valid = $this->validateIPN($ipn);

		if($valid === true)
		{
			$this->processIPN();
		}
	}

	/**
	 * Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
	 * Instead, read raw POST data from the input stream.
	 *
	 * @return string
	 */
	private function readIPN()
	{
		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);

		$myPost = array();
		foreach($raw_post_array as $keyval)
		{
			$keyval = explode('=', $keyval);
			if(count($keyval) == 2)
			{
				$myPost[$keyval[0]] = urldecode($keyval[1]);
			}
		}

		// Read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'.
		$req = 'cmd=_notify-validate';

		if(function_exists('get_magic_quotes_gpc'))
		{
			$get_magic_quotes_exists = true;
		}
		else
		{
			$get_magic_quotes_exists = false;
		}

		foreach($myPost as $key => $value)
		{
			if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1)
			{
				$value = urlencode(stripslashes($value));
			}
			else
			{
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}

		return $req;
	}

	/**
	 * POST IPN data back to PayPal to validate.
	 *
	 * @param string $req
	 * @return bool
	 */
	private function validateIPN($req = null)
	{
		if($req)
		{
			$ch = curl_init('https://www.paypal.com/cgi-bin/webscr');
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

			// In wamp-like environments that do not come bundled with root authority certificates,
			// please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set
			// the directory path of the certificate as shown below:
			// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
			if(!($res = curl_exec($ch)))
			{
				// error_log("Got " . curl_error($ch) . " when processing IPN data");
				curl_close($ch);
				exit;
			}

			curl_close($ch);

			if(strcmp($res, "VERIFIED") == 0)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Process IPN data:
	 * - Check whether the payment_status is Completed.
	 * - Check that txn_id has not been previously processed.
	 * - Check that receiver_email is your Primary PayPal email.
	 * - Check that payment_amount/payment_currency are correct.
	 * - Process the notification.
	 */
	private function processIPN()
	{
		/*
		$item_name = $_POST['item_name'];
		$item_number = $_POST['item_number'];
		$payment_status = $_POST['payment_status'];
		$payment_amount = $_POST['mc_gross'];
		$payment_currency = $_POST['mc_currency'];
		$txn_id = $_POST['txn_id'];
		$receiver_email = $_POST['receiver_email'];
		$payer_email = $_POST['payer_email'];
		*/
	}

}


new ipn_listener();
exit;
