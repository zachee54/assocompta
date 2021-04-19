<?php
$count = 10;
$messages = array('count' => 10);

// Plural
echo __n('You have {0} new message.', 'You have %d new messages.', $count);
echo __n('You deleted {0} message.', 'You deleted %d messages.', $messages['count']);

// Domain Plural
echo __dn('domain', 'You have {0} new message (domain).', 'You have %d new messages (domain).', '10');
echo __dn('domain', 'You deleted {0} message (domain).', 'You deleted %d messages (domain).', $messages['count']);

// Duplicated Message
echo __('Editing this Page');
echo __('You have %d new message.');

// Contains quotes
echo __('double "quoted"');
echo __("single 'quoted'");

// Multiline
__('Hot features!'
	. "\n - No Configuration:"
		. ' Set-up the database and let the magic begin'
	. "\n - Extremely Simple:"
		. ' Just look at the name...It\'s Cake'
	. "\n - Active, Friendly Community:"
		. ' Join us #cakephp on IRC. We\'d love to help you get started');

// Category
echo __c('You have a new message (category: LC_NUMERIC).', 4);
// LC_TIME is skipped.
echo __c('You have a new message (category: LC_TIME).', 5);

// Context
echo __('letter');
echo __x('A', 'letter');
echo __x('B', 'letter');
echo __x('A', 'letter');
echo __n('{0} letter', '%d letters', $count);
echo __xn('A', '%d letter', '%d letters', $count);
