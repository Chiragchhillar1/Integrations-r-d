<?php

return [

	'salesforce' => [
		'client_id' => env('SALESFORCE_CLIENT_ID'),
        'client_secret' => env('SALESFORCE_CLIENT_SECERT'),
        'redirect_url'	=> env('SALESFORCE_REDIRECT_URL')
	],

	'freshdesk' => [
		'domain' => env('FRESHDESK_DOMAIN'),
		'api_key' => env('FRESHDESK_API_KEY')
	],

	'zendesk' => [
		'instance_url'	=> env('ZENDESK_INSTANCE_URL'),
		'client_id' => env('ZENDESK_CLIENT_ID'),
        'client_secret' => env('ZENDESK_CLIENT_SECERT'),
        'redirect_url'	=> env('ZENDESK_REDIRECT_URL')
	],

	'hubspot' => [

	]

];