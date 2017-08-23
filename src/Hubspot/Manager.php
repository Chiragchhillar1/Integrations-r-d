<?php

namespace ChiragChhillar\Helpdesk\Hubspot;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;

class Manager
{
	public function __construct() 
	{
		$this->hubspotUrl = config('crms.hubspot.api_url');
		$this->hubspotClientId = config('crms.hubspot.client_id');
		$this->hubspotClientSecret = config('crms.hubspot.client_secret');
		$this->hubspotRedirectUrl = config('crms.hubspot.redirect_url');
	}

	public function callback()
	{
		return 'callback';
	}

	/**
	 * Get access and refresh token from hubspot
	 * @param  [type] $code [description]
	 * @return [type]       [json with tokens]
	 */
	public function getToken($code)
	{
		Redis::del('hubspot:accesstoken');
		Redis::del('hubspot:refreshtoken');
		Redis::del('hubspot:accesstokentime');
		$client = new Client();
	    $response = $client->request('POST', $this->hubspotUrl.'/oauth/v1/token', [
	                                'headers' => [
	                                    'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8'
	                                ],
	                                'form_params' => [
	                                    'grant_type' => 'authorization_code',
	                                    'code' => $code,
	                                    'client_id' => $this->hubspotClientId,
	                                    'client_secret' => $this->hubspotClientSecret,
	                                    'redirect_uri' => $this->hubspotRedirectUrl
	                                ]
	                            ]);
	    $response = json_decode($response->getBody());
	    Redis::set('hubspot:accesstoken', $response->access_token);
	    Redis::set('hubspot:refreshtoken', $response->refresh_token);
	    Redis::set('hubspot:accesstokentime', $response->expires_in);
	    return $response;
	}

	/**
	 * refresh access token from hubspot
	 * @param  [type] $code [description]
	 * @return [type]       [json with tokens]
	 */
	public function refreshAccessToken()
	{
		$refreshToken = Redis::get('hubspot:refreshtoken');
		$client = new Client();
	    $response = $client->request('POST', $this->hubspotUrl.'/oauth/v1/token', [
	                                'headers' => [
	                                    'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8'
	                                ],
	                                'form_params' => [
	                                    'grant_type' => 'refresh_token',
	                                    'client_id' => $this->hubspotClientId,
	                                    'client_secret' => $this->hubspotClientSecret,
	                                    'redirect_uri' => $this->hubspotRedirectUrl,
	                                    'refresh_token' => $refreshToken
	                                ]
	                            ]);
	    $response = json_decode($response->getBody());
	    Redis::del('hubspot:accesstoken');
		Redis::del('hubspot:refreshtoken');
		Redis::del('hubspot:accesstokentime');
	    Redis::set('hubspot:accesstoken', $response->access_token);
	    Redis::set('hubspot:refreshtoken', $response->refresh_token);
	    Redis::set('hubspot:accesstokentime', $response->expires_in);
	    return $response;
	}

	/**
	 *  fetch All contacts from hubspot
	 * @return [type] [description]
	 */
	public function getAllContacts()
	{
		$accessToken = Redis::get('hubspot:accesstoken');
		$client = new Client();
	    $response = $client->request('GET', $this->hubspotUrl.'/contacts/v1/lists/all/contacts/all',   [
	                	'headers' => [
	                                	'Authorization' => 'Bearer '.$accessToken
	                                ]
	                ]);
	    $response = json_decode($response->getBody());
	    return $response;
	}

	/**
	 *  fetch a contact info from hubspot
	 * @return [type] [description]
	 */
	public function getContact($contactId)
	{
		$accessToken = Redis::get('hubspot:accesstoken');
		$client = new Client();
	    $response = $client->request('GET', $this->hubspotUrl.'/contacts/v1/contact/vid/'.$contactId.'/profile',   [
	                	'headers' => [
	                                	'Authorization' => 'Bearer '.$accessToken
	                                ]
	                ]);
	    $response = json_decode($response->getBody());
	    return $response;
	}

	/**
	 * create Contact in hubspot
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function createContact($data)
	{
		$accessToken = Redis::get('hubspot:accesstoken');
		$client = new Client();
	    $response = $client->request('POST', $this->hubspotUrl.'/contacts/v1/contact',   [
	                	'headers' => [
	                                	'Authorization' => 'Bearer '.$accessToken,
	                                	'Content-Type' => 'application/json'
	                                ],
	                    'json' => $data
	                ]);
	    $response = json_decode($response->getBody());
	    return $response;
	}
}