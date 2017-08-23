<?php 

namespace ChiragChhillar\Helpdesk;

use GuzzleHttp\Client;

class Helpdesk
{
	public static function salesforce()
	{
		return new \ChiragChhillar\Helpdesk\Salesforce\Manager();
	}

	public static function zendesk()
	{
		return new \ChiragChhillar\Helpdesk\Zendesk\Manager();
	}

	public static function freshdesk()
	{
		return new \ChiragChhillar\Helpdesk\Freshdesk\Manager();
	}

	public static function hubspot()
	{
		return new \ChiragChhillar\Helpdesk\Hubspot\Manager();
	}
}