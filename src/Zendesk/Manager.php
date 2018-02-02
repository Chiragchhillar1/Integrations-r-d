<?php

namespace ChiragChhillar\Helpdesk\Zendesk;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;

class Manager
{
	public function __construct() 
	{
		$this->zendeskUrl = config('crms.zendesk.instance_url');
		$this->zendeskClientId = config('crms.zendesk.client_id');
		$this->zendeskClientSecret = config('crms.zendesk.client_secret');
		$this->zendeskRedirectUrl = config('crms.zendesk.redirect_url');
	}

	/**
	 * Get access and refresh token from zendesk
	 * @param  [type] $code [description]
	 * @return [type]       [json with tokens]
	 */
	public function getToken($code)
	{
		$client = new Client();
	    $response = $client->request('POST', $this->zendeskUrl.'/oauth/tokens', [
	                                'headers' => [
	                                    'Content-Type' => 'application/json'
	                                ],
	                                'json' => [
	                                    'grant_type' => 'authorization_code',
	                                    'code' => $code,
	                                    'client_id' => $this->zendeskClientId,
	                                    'client_secret' => $this->zendeskClientSecret,
	                                    'redirect_uri' => $this->zendeskRedirectUrl,
	                                    'scope' => 'read write'
	                                ]
	                            ]);
	    return $response;
	}
//////////////////////////////////////////////////////////////////////////////////	

	/**
	 * zendesk	fetch knowledge base
	 * * @return [type] our style json output for articles
	 */
	public function allArticles($accessToken, $instanceUrl, $userName)
	{
		$client = new Client();
	    $response = $client->request('GET', $instanceUrl.'/api/v2/help_center/articles.json', [
                        'headers' => [
		                'Content-Type' => 'application/json'
		            ],
		            'auth' => [
		                $userName, 
		                $accessToken
		            ]
	            ]);
	    $response = json_decode($response->getBody(), true);
	    return $response;
	}

	/**
	 * zendesk	fetch knowledge base all categories
	 * * @return [type] our style json output for articles
	 */
	public function categories($accessToken)
	{
		$client = new Client();
	    $response = $client->request('GET', $this->zendeskUrl.'/api/v2/help_center/categories.json', [
	                                'headers' => [
	                                    'Authorization' => 'Bearer '.$accessToken
	                                ]
	                            ]);
	    $response = json_decode($response->getBody());
	    return $response;
	}

	/**
	 * zendesk	fetch knowledge base a single category
	 * * @return [type] our style json output for articles
	 */
	public function category($accessToken, $instanceUrl, $userName, $categoryId)
	{
		$client = new Client();
	    $response = $client->request('GET', $instanceUrl.'/api/v2/help_center/categories/'.$categoryId.'.json', [
                        'headers' => [
		                'Content-Type' => 'application/json'
		            ],
		            'auth' => [
		                $userName, 
		                $accessToken
		            ]
                ]);
	    $response = json_decode($response->getBody(), true);
	    return $response;
	}

	/**
	 * zendesk	fetch knowledge base all sections of a category
	 * * @return [type] our style json output for articles
	 */
	public function sections($accessToken, $instanceUrl, $userName, $categoryId)
	{
		$client = new Client();
	    $response = $client->request('GET', $instanceUrl.'/api/v2/help_center/categories/'.$categoryId.'/sections.json', [
                                'headers' => [
				                'Content-Type' => 'application/json'
				            ],
				            'auth' => [
				                $userName, 
				                $accessToken
				            ]
                        ]);
	    $response = json_decode($response->getBody());
	    return $response;
	}

	/**
	 * zendesk	fetch knowledge base all articles of a section
	 * * @return [type] our style json output for articles
	 */
	public function articles($accessToken, $instanceUrl, $userName, $sectionId)
	{
		$client = new Client();
	    $response = $client->request('GET', $instanceUrl.'/api/v2/help_center/sections/'.$sectionId.'/articles.json', [
                                'headers' => [
				                'Content-Type' => 'application/json'
				            ],
				            'auth' => [
				                $userName, 
				                $accessToken
				            ]
	                    ]);
	    $response = json_decode($response->getBody());
	    return $response;
	}

	/**
	 * zendesk	fetch knowledge base single section
	 * * @return [type] our style json output for articles
	 */
	public function section($accessToken, $sectionId)
	{
		$client = new Client();
	    $response = $client->request('GET', $this->zendeskUrl.'/api/v2/help_center/sections/'.$sectionId.'.json', [
	                                'headers' => [
	                                    'Authorization' => 'Bearer '.$accessToken
	                                ]
	                            ]);
	    $response = json_decode($response->getBody(), true);
	    return $response;
	}

//////////////////////////////////////////////////////////////////////////////////
	/**
	 *  Create ticket in zendesk
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function createTicket($accessToken, $instanceUrl, $userName, $data)
	{
		$client = new Client();
        $response = $client->request('POST', $instanceUrl.'/api/v2/tickets.json', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'auth' => [
                $userName, 
                $accessToken
            ],
            'json' => $data
        ]);
        $response = json_decode($response->getBody(), true);
        return $response;
	}

	/**
	 * get a ticket
	 * @param  [type] $ticketId [description]
	 * @return [type]           [description]
	 */
	public function getTicket($accessToken, $instanceUrl, $userName, $ticketId)
	{
		$client = new Client();
        $response = $client->request('GET', $instanceUrl.'/api/v2/tickets/'.$ticketId.'.json', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'auth' => [
                $userName, 
                $accessToken
            ]
        ]);
        $response = json_decode($response->getBody(), true);
        return $response;
	}

	/**
	 * get a user tickets
	 * @param  [type] $ticketId [description]
	 * @return [type]           [description]
	 */
	public function getUserTicket($accessToken, $instanceUrl, $userName, $requesterId)
	{
		$client = new Client();
        $response = $client->request('GET', $instanceUrl.'/api/v2/users/'.$requesterId.'/tickets/requested.json', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'auth' => [
                $userName, 
                $accessToken
            ]
        ]);
        $response = json_decode($response->getBody(), true);
        return $response;
	}

	/**
	 *  Update ticket in zendesk
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function updateTicket($accessToken, $instanceUrl, $userName, $ticketId, $data)
	{
		$client = new Client();
        $response = $client->request('PUT', $instanceUrl.'/api/v2/tickets/'.$ticketId.'.json', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'auth' => [
                $userName, 
                $accessToken
            ],
            'json' => $data
        ]);
        $response = json_decode($response->getBody(), true);
        return $response;
	}

	/**
	 * get list of agents in zendesk
	 * @return [type] [description]
	 */
	public function getAgents($accessToken, $instanceUrl, $userName)
	{
		$client = new Client();
        $response = $client->request('GET', $instanceUrl.'/api/v2/users.json?role[]=agent', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'auth' => [
                $userName, 
                $accessToken
            ]
        ]);
        $response = json_decode($response->getBody(), true);
        return $response;
	}

	/**
	 *  Create Enduser in zendesk
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function createEndUser($data)
	{
		$accessToken = Redis::get('zendesk:accesstoken');

		$client = new Client();
        $response = $client->request('POST', $this->zendeskUrl.'/api/v2/users/create_or_update.json', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$accessToken
            ],
            'json' => $data
        ]);
        $response = json_decode($response->getBody(), true);
        return $response;
	}
}