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
		if(USER)
		{
			// Get plugin preferences.
			$this->plugPrefs = e107::getPlugConfig('paypal_donation')->getPref();
			// Render menu.
			$this->renderMenu();
		}
	}

	/**
	 *
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
	 *
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
	 *
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

			if(isset($ipn['payer_id']))
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

}


new paypal_donation_menu();
