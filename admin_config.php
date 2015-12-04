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
		'main'     => array(
			'controller' => 'paypal_donation_admin_main_ui',
			'path'       => null,
		),
		'donation' => array(
			'controller' => 'paypal_donation_admin_donation_ui',
			'path'       => null,
		)
	);

	protected $adminMenu = array(
		'main/prefs'      => array(
			'caption' => LAN_PAYPAL_DONATION_ADMIN_01,
			'perm'    => 'P',
		),
		'donation/list'   => array(
			'caption' => LAN_PAYPAL_DONATION_ADMIN_11,
			'perm'    => 'P',
		),
		'donation/create' => array(
			'caption' => LAN_PAYPAL_DONATION_ADMIN_12,
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


class paypal_donation_admin_donation_ui extends e_admin_ui
{

	protected $pluginTitle = LAN_PLUGIN_PAYPAL_DONATION_NAME;
	protected $pluginName  = 'paypal_donation';
	protected $eventName   = 'paypal-donation';
	protected $table       = "paypal_donation";
	protected $pid         = "id";
	protected $perPage     = 0;
	protected $batchDelete = false;
	protected $listOrder   = "title ASC";

	protected $fields = array(
		'checkboxes'    => array(
			'title'   => '',
			'type'    => null,
			'width'   => '5%',
			'forced'  => true,
			'thclass' => 'center',
			'class'   => 'center',
		),
		'id'            => array(
			'title'    => LAN_PAYPAL_DONATION_ADMIN_13,
			'type'     => 'number',
			'width'    => '5%',
			'forced'   => true,
			'readonly' => true,
			'thclass'  => 'center',
			'class'    => 'center',
		),
		'title'         => array(
			'title'    => LAN_PAYPAL_DONATION_ADMIN_14,
			'type'     => 'text',
			'inline'   => true,
			'width'    => 'auto',
			'thclass'  => 'left',
			'readonly' => false,
			'validate' => true,
		),
		'description'   => array(
			'title'     => LAN_PAYPAL_DONATION_ADMIN_15,
			'type'      => 'textarea',
			'inline'    => true,
			'width'     => 'auto',
			'thclass'   => 'left',
			'readParms' => 'expand=...&truncate=150&bb=1',
			'readonly'  => false,
		),
		'custom_amount' => array(
			'title'      => LAN_PAYPAL_DONATION_ADMIN_20,
			'type'       => 'boolean',
			'writeParms' => 'label=yesno',
			'data'       => 'int',
		),
		'goal_amount'   => array(
			'title'   => LAN_PAYPAL_DONATION_ADMIN_21,
			'type'    => 'number',
			'width'   => 'auto',
			'thclass' => 'center',
			'class'   => 'center',
		),
		'goal_date'     => array(
			'title'   => LAN_PAYPAL_DONATION_ADMIN_22,
			'type'    => 'date',
			'width'   => 'auto',
			'thclass' => 'center',
			'class'   => 'center',
		),
		'currency'      => array(
			'title'      => LAN_PAYPAL_DONATION_ADMIN_23,
			'type'       => 'dropdown',
			'width'      => 'auto',
			'readonly'   => false,
			'inline'     => false,
			'filter'     => true,
			'writeParms' => array(),
			'readParms'  => array(),
			'thclass'    => 'center',
			'class'      => 'center',
		),
		'status'        => array(
			'title'      => LAN_PAYPAL_DONATION_ADMIN_16,
			'type'       => 'dropdown',
			'width'      => 'auto',
			'readonly'   => false,
			'inline'     => true,
			'batch'      => true,
			'filter'     => true,
			'writeParms' => array(
				1 => LAN_PAYPAL_DONATION_ADMIN_17,
				0 => LAN_PAYPAL_DONATION_ADMIN_18,
			),
			'readParms'  => array(
				1 => LAN_PAYPAL_DONATION_ADMIN_17,
				0 => LAN_PAYPAL_DONATION_ADMIN_18,
			),
			'thclass'    => 'center',
			'class'      => 'center',
		),
		'options'       => array(
			'title'   => LAN_PAYPAL_DONATION_ADMIN_19,
			'type'    => null,
			'width'   => '10%',
			'forced'  => true,
			'thclass' => 'center last',
			'class'   => 'center',
			'sort'    => true,
		),
	);

	protected $fieldpref = array(
		'checkboxes',
		'id',
		'title',
		'description',
		'status',
		'options',
	);

	function init()
	{

	}

	function afterCreate($newdata, $olddata, $id)
	{
		// Insert amount items to "paypal_donation_amount" table.
	}

	function beforeCreate($newdata, $olddata)
	{
		// Validate amount items?
	}

	function beforeUpdate($newdata, $olddata)
	{
		// Validate amount items?
	}

	function afterUpdate($newdata, $olddata, $id)
	{
		// Update amount items in "paypal_donation_amount" table.
	}

	public function afterDelete($deleted_data, $id, $deleted_check)
	{
		// Delete amount items from "paypal_donation_amount" table.
	}

}


new paypal_donation_admin();

require_once(e_ADMIN . 'auth.php');
e107::getAdminUI()->runPage();
require_once(e_ADMIN . 'footer.php');
exit;
