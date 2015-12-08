<?php

/**
 * @file
 * Class installation to define shortcodes.
 */

if(!defined('e107_INIT'))
{
	exit;
}

// [PLUGINS]/paypal_donation/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('paypal_donation', false, true);


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
		$percent = $raised / ($goal / 100);
		$formatted = number_format($percent, 0, '.', '');
		return $formatted;
	}

	function sc_percent_text()
	{
		$raised = (float) $this->var['raised']['amount'];
		$goal = (float) $this->var['menu_item']['pd_goal_amount'];
		$percent = $raised / ($goal / 100);
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

		if((int) $goalDate > 0)
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
		$menuItem = $this->var['menu_item'];
		$amounts = $this->var['amounts'];

		$html = '';

		if(check_class($menuItem['pd_donate']) === false)
		{
			return $html;
		}

		if((int) $menuItem['pd_custom_amount'] == 1 || !empty($amounts))
		{
			$form = e107::getForm();
			$msgs = e107::getMessage();

			$html .= $msgs->render();

			$html .= $form->open('paypal-donation-form', 'post', e_SELF, array(
				'class' => 'paypal-donation-form',
			));
			$html .= '<div class="form-group">';
			$html .= '<label>' . LAN_PAYPAL_DONATION_FRONT_14 . '</label>';

			foreach($amounts as $key => $amount)
			{
				$first = ($key == 0 ? ' first' : '');
				$html .= '<div class="radio' . $first . '">';
				$html .= '<label>';
				$html .= $form->radio('amount', $amount['pda_value']);
				$html .= $amount['pda_label'];
				$html .= '</label>';
				$html .= '</div>';
			}

			if((int) $menuItem['pd_custom_amount'] == 1)
			{
				$html .= '<div class="radio">';
				$html .= '<label>';
				$html .= $form->radio('amount', 'custom');
				$html .= $form->text('custom_amount', '', 80, array(
					'class'       => 'input-sm',
					'placeholder' => LAN_PAYPAL_DONATION_FRONT_12,
				));
				$html .= '</label>';
				$html .= '</div>';
			}

			$html .= '</div>';
			$html .= '<div class="form-group actions text-center">';
			$html .= $form->hidden('donation', 1);
			$html .= $form->hidden('donation_item', $menuItem['pd_id']);
			$html .= $form->submit('submit', LAN_PAYPAL_DONATION_FRONT_13);
			$html .= '</div>';

			$html .= $form->close();
		}

		return $html;
	}

}
