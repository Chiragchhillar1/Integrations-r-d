<?php 

namespace ChiragChhillar\Helpdesk\Facades;

use Illuminate\Support\Facades\Facade;

class Helpdesk extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'helpdesk';
	}
}