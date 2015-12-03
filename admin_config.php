<?php

/**
 * @file
 * Class installations to handle configuration forms on Admin UI.
 */

require_once("../../class2.php");

if(!e107::isInstalled('paypal_donation') || !getperms("P"))
{
	header("Location: " . e_BASE . "index.php");
	exit;
}

// [PLUGINS]/paypal_donation/languages/[LANGUAGE]/[LANGUAGE]_admin.php
e107::lan('paypal_donation', true, true);


/**
 * Class paypal_donation_admin.
 */
class paypal_donation_admin extends e_admin_dispatcher
{

	protected $modes = array(
		'main' => array(
			'controller' => 'paypal_donation_admin_main_ui',
			'path'       => null,
		),
	);

	protected $adminMenu = array(
		'main/prefs' => array(
			'caption' => LAN_PAYPAL_DONATION_ADMIN_01,
			'perm'    => 'P',
		),
	);

	protected $menuTitle = LAN_PLUGIN_PAYPAL_DONATION_NAME;
}


/**
 * Class paypal_donation_admin_main_ui.
 */
class paypal_donation_admin_main_ui extends e_admin_ui
{

	protected $pluginTitle = LAN_PLUGIN_PAYPAL_DONATION_NAME;
	protected $pluginName  = "paypal_donation";
	protected $preftabs    = array(
		LAN_PAYPAL_DONATION_ADMIN_01,
	);
	protected $prefs       = array(
		'sandbox_mode'       => array(
			'title'      => LAN_PAYPAL_DONATION_ADMIN_02,
			'type'       => 'boolean',
			'data'       => 'int',
			'writeParms' => array(
				0 => LAN_PAYPAL_DONATION_ADMIN_03,
				1 => LAN_PAYPAL_DONATION_ADMIN_04,
			),
			'tab'        => 0,
		),
		'email_sandbox'      => array(
			'title' => LAN_PAYPAL_DONATION_ADMIN_06,
			'type'  => 'text',
			'data'  => 'str',
			'tab'   => 0,
		),
		'email_live'         => array(
			'title' => LAN_PAYPAL_DONATION_ADMIN_05,
			'type'  => 'text',
			'data'  => 'str',
			'tab'   => 0,
		),
		'validate_email'     => array(
			'title'       => LAN_PAYPAL_DONATION_ADMIN_07,
			'description' => LAN_PAYPAL_DONATION_ADMIN_08,
			'type'        => 'boolean',
			'writeParms'  => 'label=yesno',
			'data'        => 'int',
			'tab'         => 0,
		),
		'logging_failed_ipn' => array(
			'title'       => LAN_PAYPAL_DONATION_ADMIN_09,
			'description' => LAN_PAYPAL_DONATION_ADMIN_10,
			'type'        => 'boolean',
			'writeParms'  => 'label=yesno',
			'data'        => 'int',
			'tab'         => 0,
		),
	);
}


new paypal_donation_admin();

require_once(e_ADMIN . 'auth.php');
e107::getAdminUI()->runPage();
require_once(e_ADMIN . 'footer.php');
exit;
