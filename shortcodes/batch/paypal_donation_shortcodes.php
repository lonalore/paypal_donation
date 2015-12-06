<?php

/**
 * @file
 * Class installation to define shortcodes.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class paypal_donation_shortcodes.
 */
class paypal_donation_shortcodes extends e_shortcode
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
		parent::__construct();

		// Get plugin preferences.
		$this->plugPrefs = e107::getPlugConfig('paypal_donation')->getPref();
	}

	function sc_menu_id()
	{
		return $this->var['menu_item']['pd_id'];
	}

	function sc_title()
	{
		return $this->var['menu_item']['pd_title'];
	}

	function sc_description()
	{
		$tp = e107::getParser();
		$description = $tp->toHTML($this->var['menu_item']['pd_description'], true);
		return $description;
	}

	function sc_currency()
	{
		return $this->var['menu_item']['pd_currency'];
	}

	function sc_raised_amount()
	{
		$amount = $this->var['raised']['amount'];
		$currency = $this->var['menu_item']['pd_currency'];

		if(in_array($currency, array('HUF', 'JPY', 'TWD')))
		{
			$formatted = number_format($amount, 0, '.', ',');
		}
		else
		{
			$formatted = number_format($amount, 2, '.', ',');
		}

		return $formatted;
	}

	function sc_raised_by()
	{
		$x = (int) $this->var['raised']['by'];

		if($x > 1)
		{
			$text = '<strong>' . $x . '</strong> ' . LAN_PAYPAL_DONATION_FRONT_04;
		}
		else
		{
			$text = '<strong>' . $x . '</strong> ' . LAN_PAYPAL_DONATION_FRONT_03;
		}

		$text = str_replace('[x]', $text, LAN_PAYPAL_DONATION_FRONT_02);
		return $text;
	}

	function sc_percent()
	{
		$raised = (float) $this->var['raised']['amount'];
		$goal = (float) $this->var['menu_item']['pd_goal_amount'];
		$percent = $goal / 100 * $raised;
		$formatted = number_format($percent, 0);
		return $formatted;
	}

	function sc_percent_text()
	{
		$raised = (float) $this->var['raised']['amount'];
		$goal = (float) $this->var['menu_item']['pd_goal_amount'];
		$percent = $goal / 100 * $raised;
		$formatted = number_format($percent, 0);
		$text = str_replace('[x]', '<strong>' . $formatted . '%</strong>', LAN_PAYPAL_DONATION_FRONT_05);
		return $text;
	}

	function sc_goal()
	{
		$amount = $this->var['menu_item']['pd_goal_amount'];
		$currency = $this->var['menu_item']['pd_currency'];

		if(in_array($currency, array('HUF', 'JPY', 'TWD')))
		{
			$formatted = number_format($amount, 0, '.', ',');
		}
		else
		{
			$formatted = number_format($amount, 2, '.', ',');
		}

		$text = LAN_PAYPAL_DONATION_FRONT_06 . ' <strong>' . $formatted . ' ' . $currency . '</strong>';

		$goalDate = $this->var['menu_item']['pd_goal_date'];
		if((int) $goalDate > 0)
		{
			$format = isset($this->plugPrefs['date_format']) ? $this->plugPrefs['date_format'] : 'short';
			$date = e107::getDate();
			$text .= ' ' . LAN_PAYPAL_DONATION_FRONT_10;
			$text .= ' <strong>' . $date->convert_date($goalDate, $format) . '</strong>';
		}

		return $text;
	}

	function sc_days_left()
	{
		$goalDate = $this->var['menu_item']['pd_goal_date'];
		$now = time();

		$left = '';

		if(isset($goalDate) && $goalDate > 0)
		{
			$diff = $goalDate - $now;

			if($diff > 0)
			{
				$inDay = (int) ($diff / 86400);

				if($inDay > 0)
				{
					if($inDay > 1)
					{
						$left = '<strong>' . $inDay . '</strong> ' . LAN_PAYPAL_DONATION_FRONT_08;
					}
					else
					{
						$left = '<strong>' . $inDay . '</strong> ' . LAN_PAYPAL_DONATION_FRONT_07;
					}

					$left .= ' ' . LAN_PAYPAL_DONATION_FRONT_09;
				}
			}
		}

		return $left;
	}

	function sc_donation_form()
	{

	}

}
