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
			if((int) $this->plugPrefs['sandbox_mode'] === 1)
			{
				$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			}
			else
			{
				$url = 'https://www.paypal.com/cgi-bin/webscr';
			}

			$ch = curl_init($url);
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
	 * - Done: Check whether the payment_status is Completed.
	 * - Done: Check that txn_id has not been previously processed.
	 * - Done: Check that receiver_email is your Primary PayPal email.
	 * - Done: Check that the "custom" value is a valid menu item.
	 * - Done: Process the notification.
	 */
	private function processIPN()
	{
		$log = e107::getLog();
		$db = e107::getDb();
		$tp = e107::getParser();

		$logging = (bool) $this->plugPrefs['logging_failed_ipn'];
		$validate_email = (bool) $this->plugPrefs['validate_email'];

		// Check whether the payment_status is Completed.
		$payment_status = vartrue($_POST['payment_status']);

		if(strtoupper($payment_status) != 'COMPLETED')
		{
			if($logging)
			{
				$log->add('Status is not completed', $_POST, E_LOG_INFORMATIVE, 'IPN');
			}
			exit;
		}

		// Check that txn_id has not been previously processed.
		$txn_id = vartrue($_POST['txn_id'], 0);
		$exists = $db->count('paypal_donation_ipn', '(*)', 'pdi_txn_id = "' . $tp->toDB($txn_id) . '" ');

		if($exists > 0)
		{
			if($logging)
			{
				$log->add('Existing TXN ID', $_POST, E_LOG_INFORMATIVE, 'IPN');
			}
			exit;
		}

		// Check that receiver_email is your Primary PayPal email.
		if($validate_email)
		{
			$receiver_email = vartrue($_POST['receiver_email']);

			if((int) $this->plugPrefs['sandbox_mode'] === 1)
			{
				$business = $this->plugPrefs['email_sandbox'];
			}
			else
			{
				$business = $this->plugPrefs['email_live'];
			}

			if($receiver_email != $business)
			{
				if($logging)
				{
					$log->add('Invalid receiver email', $_POST, E_LOG_INFORMATIVE, 'IPN');
				}
				exit;
			}
		}

		// Check that the "custom" value is a valid menu item.
		$custom = vartrue($_POST['custom'], '');
		$segments = explode('|', $custom);

		$pd_id = (int) $segments[0];
		$exists = $db->count('paypal_donation', '(*)', 'pd_id = ' . $pd_id . ' ');

		if($exists == 0)
		{
			if($logging)
			{
				$data = array(
					'POST'     => $_POST,
					'SEGMENTS' => $segments,
					'PD_ID'    => $pd_id,
				);

				$log->add('Invalid menu item', $data, E_LOG_INFORMATIVE, 'IPN');
			}
			exit;
		}

		$pdi_user = vartrue($segments[1], 0);
		$pdi_mc_gross = vartrue($_POST['mc_gross'], 0);
		$pdi_mc_fee = vartrue($_POST['mc_fee'], 0);
		$pdi_mc_currency = vartrue($_POST['mc_currency'], '');
		$pdi_payment_date = vartrue($_POST['payment_date'], '');
		$pdi_serialized_ipn = serialize($_POST);

		// Process IPN.
		$data = array(
			'pdi_donation'       => (int) $pd_id,
			'pdi_user'           => (int) $pdi_user,
			'pdi_txn_id'         => $tp->toDB($txn_id),
			'pdi_mc_gross'       => (float) $pdi_mc_gross,
			'pdi_mc_fee'         => (float) $pdi_mc_fee,
			'pdi_mc_currency'    => $tp->toDB($pdi_mc_currency),
			'pdi_payment_date'   => strtotime($pdi_payment_date),
			'pdi_cancelled'      => 0,
			'pdi_serialized_ipn' => $pdi_serialized_ipn,
		);

		$id = $db->insert('paypal_donation_ipn', $data);

		if($id)
		{
			$event = e107::getEvent();
			$event->trigger('paypal-donation-ipn-insert', $id);
			exit;
		}
		else
		{
			if($logging)
			{
				$log->add('SQL Insert', $_POST, E_LOG_INFORMATIVE, 'IPN');
			}
			exit;
		}
	}

}


new ipn_listener();
exit;
