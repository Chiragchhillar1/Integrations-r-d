<?php

namespace ChiragChhillar\Helpdesk\Servicenow;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;

class Manager
{
	/**
	 * get our app details
	 */
	public function __construct() 
	{
		$this->servicenowClientId = config('crms.servicenow.client_id');
		$this->servicenowClientSecret = config('crms.servicenow.client_secret');
		$this->servicenowRedirectUrl = config('crms.servicenow.redirect_url');
	}

	/**
	 * Get access and refresh token from Servicenow
	 * @param  [type] $code [description]
	 * @return [type]       [json with tokens]
	 */
	public function getToken($code)
	{
		$client = new Client();
                
        $post_data = [
               'grant_type' => 'authorization_code',
               'code' => $code,
               'redirect_uri' => $this->servicenowRedirectUrl,
               'scope' => 'useraccount'
           ];

        $auth = base64_encode($this->servicenowClientId.':'.$this->servicenowClientSecret);

        $tokens = $client->request('POST', 'https://dev34876.service-now.com/oauth_token.do', [
           'headers' => [
               'Authorization' => 'Basic ' . $auth,
               'Content-Type' => 'application/x-www-form-urlencoded'
           ],
           'form_params' => $post_data
        ]);
        return $tokens;
	}

	/**
     * refresh tokens from servicenow
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function refreshTokens($refreshToken)
    {
        $client = new Client();
            
        $post_data = [
               'grant_type' => 'refresh_token',
               'redirect_uri' => $this->servicenowRedirectUrl,
               'scope' => 'useraccount',
               'refresh_token' => $refreshToken
           ];

       $auth = base64_encode($this->servicenowClientId.':'.$this->servicenowClientSecret);

       $response = $client->request('POST', 'https://dev34876.service-now.com/oauth_token.do', [
           'headers' => [
               'Authorization' => 'Basic ' . $auth,
               'Content-Type' => 'application/x-www-form-urlencoded'
           ],
           'form_params' => $post_data
       ]);

       return $response;
    }	

	/**
  	 * Get knowledgebase from servicenow
  	 * @return [type] [description]
  	 */
    public function fetchKnowledgeBase($accessToken)
    {
    	$client = new Client();
        $knowledgeBase = $client->request('GET', 'https://dev34876.service-now.com/api/now/table/kb_knowledge', [
            'headers' => [
                'Accept' 		=> 'application/json',
				'Authorization' => 'Bearer '.$accessToken
            ]
        ]);
        return $knowledgeBase;
    }
}