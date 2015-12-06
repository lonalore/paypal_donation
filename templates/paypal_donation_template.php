<?php

/**
 * @file
 * Templates for plugin displays.
 */

$PAYPAL_DONATION_TEMPLATE['MENU'] = '
<div class="paypal-donation-menu-item" id="paypal-donation-menu-{MENU_ID}">
	<div class="donation-title">
		{TITLE}
	</div>

	<div class="donation-description">
		{DESCRIPTION}
	</div>

	<div class="raised-amount">
		<span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> {RAISED_AMOUNT} <span class="currency">{CURRENCY}</span>
	</div>
	<div class="raised-by">
		{RAISED_BY}
	</div>

	<div class="progress-container">
		<div class="progress">
		  <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="{PERCENT}" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em; width: {PERCENT}%">
		    {PERCENT}%
		  </div>
		</div>

		<div class="percent-text pull-left">
			{PERCENT_TEXT}
		</div>

		<div class="days-left pull-right">
			{DAYS_LEFT}
		</div>

		<div class="clear"></div>
	</div>

	<div class="donation-goal">
		<span class="glyphicon glyphicon-flag" aria-hidden="true"></span> {GOAL}
	</div>

	<div class="donation-form">
		{DONATION_FORM}
	</div>
</div>
';
