<?php

/**
 * @file
 * Class to render e107 menu for plugin.
 */

if(!defined('e107_INIT'))
{
	exit;
}

if(!e107::isInstalled('paypal_donation'))
{
	exit;
}

// [PLUGINS]/paypal_donation/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('paypal_donation', false, true);


/**
 * Class paypal_donation_menu.
 */
class paypal_donation_menu
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

		if(vartrue($_POST['donation'], false))
		{
			$this->formSubmit();
		}

		// Render menu.
		$this->renderMenu();
	}

	/**
	 * Render menu contents.
	 */
	function renderMenu()
	{
		$template = e107::getTemplate('paypal_donation');
		$sc = e107::getScBatch('paypal_donation', true);
		$tp = e107::getParser();
		$db = e107::getDb();

		$db->select('paypal_donation', '*', 'pd_status = 1 ORDER BY pd_weight ASC');

		$text = '';

		while($row = $db->fetch())
		{
			$item = array(
				'menu_item' => $row,
				'amounts'   => $this->getAmounts($row['pd_id']),
				'raised'    => $this->getRaised($row['pd_id']),
			);

			$sc->setVars($item);
			$text .= $tp->parseTemplate($template['MENU'], true, $sc);
		}

		e107::getRender()->tablerender(LAN_PAYPAL_DONATION_FRONT_01, $text);
		unset($text);
	}

	/**
	 * Get available amounts for donation form.
	 */
	function getAmounts($pd_id = 0)
	{
		$amounts = array();

		if((int) $pd_id === 0)
		{
			return $amounts;
		}

		$db = e107::getDb();
		$db->select('paypal_donation_amount', '*', 'pda_donation = ' . (int) $pd_id . ' ORDER BY pda_weight ASC');

		while($row = $db->fetch())
		{
			$amounts[] = $row;
		}

		return $amounts;
	}

	/**
	 * Get raised amount for a donation menu item.
	 */
	function getRaised($pd_id = 0)
	{
		$raised = array(
			'amount' => 0,
			'by'     => 0,
		);

		if((int) $pd_id === 0)
		{
			return $raised;
		}

		$db = e107::getDb();
		$db->select('paypal_donation_ipn', '*', 'pdi_donation = ' . (int) $pd_id);

		$payers = array();

		while($row = $db->fetch())
		{
			$amount = $row['pdi_mc_gross'] - $row['pdi_mc_fee'];
			$raised['amount'] += $amount;

			$ipn = unserialize($row['pdi_serialized_ipn']);

			if(vartrue($ipn['payer_id']))
			{
				if(!in_array($ipn['payer_id'], $payers))
				{
					$payers[] = $ipn['payer_id'];
				}
			}
		}

		$raised['by'] = count($payers);

		return $raised;
	}

	/**
	 * Validate submitted donation form.
	 */
	function formValidate()
	{
		$pd_id = (int) vartrue($_POST['donation_item'], 0);
		$amount = vartrue($_POST['amount'], false);

		if($pd_id === 0 || $amount === false)
		{
			return false;
		}

		if($amount != 'custom' && (float) $amount == 0)
		{
			return false;
		}

		if($amount == 'custom' && (float) $_POST['custom_amount'] == 0)
		{
			return false;
		}

		$db = e107::getDb();
		$db->select('paypal_donation', '*', 'pd_id = ' . $pd_id);

		$item = false;
		while($row = $db->fetch())
		{
			$item = $row;
		}

		if(!$item)
		{
			return false;
		}

		return true;
	}

	/**
	 * Process submitted donation form.
	 */
	function formSubmit()
	{
		$db = e107::getDb();
		$db->select('paypal_donation', '*', 'pd_id = ' . (int) vartrue($_POST['donation_item'], 0));

		$item = false;
		while($row = $db->fetch())
		{
			$item = $row;
		}

		$params = array();
		$params['cmd'] = '_donations';
		$params['item_name'] = $item['pd_title'];

		if((int) $this->plugPrefs['sandbox_mode'] === 0)
		{
			$business = $this->plugPrefs['email_sandbox'];
		}
		else
		{
			$business = $this->plugPrefs['email_live'];
		}

		// PayPal email:
		$params['business'] = $business;
		// PayPal will send an IPN notification to this URL:
		$params['notify_url'] = SITEURLBASE . e_PLUGIN_ABS . 'paypal_donation/ipn_listener.php';
		// The return page to which the user is navigated after the donations is complete:
		$params['return'] = e_SELF;
		// Signifies that the transaction data will be passed to the return page by POST:
		$params['rm'] = 2;

		// General configuration variables for the PayPal landing page.
		$params['no_note'] = 1;
		$params['cbt'] = LAN_PAYPAL_DONATION_FRONT_15; // Go Back To The Site
		$params['no_shipping'] = 1;
		$params['lc'] = 'US';
		$params['currency_code'] = $item['pd_currency'];
		$params['amount'] = ($_POST['amount'] == 'custom' ? $_POST['custom_amount'] : $_POST['amount']);
		$params['bn'] = 'PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest';
	}

}


new paypal_donation_menu();
