<?php


Route::get('demo', function () {
	return Helpdesk::salesforce()->callback();
});