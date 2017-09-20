<?php

namespace ChiragChhillar\Helpdesk\Salesforce;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;

class Manager
{
	public function __construct() 
	{

	}

	/**
	 * Get access and refresh token from salesforce
	 * @param  [type] $code [description]
	 * @return [type]       [json with tokens]
	 */
	public function getToken($code)
	{
		$client = new Client();
	    $accessToken = $client->request('POST', 'https://login.salesforce.com/services/oauth2/token', [
	                                'form_params' => [
	                                    'grant_type' 	=> 'authorization_code',
	                                    'code' 		 	=> $code,
	                                    'client_id'  	=> config('services.salesforce.client_id'),
	                                    'client_secret' => config('services.salesforce.client_secret'),
	                                    'redirect_uri' => config('services.salesforce.redirect_uri')
	                                ]
	                            ]);
	    $accessToken = json_decode($accessToken->getBody(), true);
	    return $accessToken;

	    //here to hit
	    $client = new Client();
        $articles = $client->request('POST', 'https://85ac234e.ngrok.io/api/v1/integrations/salesforce/integrate', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
            	'response' => $accessToken
            ]
        ]);
	}

	/**
	 * fetch knowledge base from salesforce
	 * @return [type] our style json output for articles
	 */
	public function fetchKnowledgeBase($accessToken, $instanceUrl)
	{
		$instanceUrl = $instanceUrl.'/services/data/v39.0/support/knowledgeArticles';

        $response = $this->callSalesforce($accessToken, $instanceUrl);
        $articles = $response['articles'];
        $layoutArticles = [];
        foreach ($articles as $index => $article) {
            $articleDetails = $this->articleDetails($accessToken, $instanceUrl, $article['id']);
            $layoutArticles[$index]['question'] = $articleDetails['title'];
            $layoutArticles[$index]['summary'] = $articleDetails['summary'];
            $layoutArticles[$index]['article_id'] = $article['id'];
            foreach ($articleDetails['layoutItems'] as $item) {
                if (!is_null($item['value'])) {
                    $layoutArticles[$index]['items'][] = $item['value'];
                }
            }
        }

        return $layoutArticles;
	}

	public function articleDetails($accessToken, $instanceUrl, $articleId)
	{
		$instanceUrl = $instanceUrl.'/'.$articleId;

        $details = $this->callSalesforce($accessToken, $instanceUrl);

        return $details;
	}

	public function callSalesforce($accessToken, $instanceUrl)
	{
		$client = new Client();
        $articles = $client->request('GET', $instanceUrl, [
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
                'Authorization' => 'Bearer '.$accessToken,
                'Accept' => 'application/json',
                'Accept-Language' => 'en-US'
            ]
        ]);
        $response = json_decode($articles->getBody(), true);
        return $response;
	}

	/**
	 * create lead in salesforce
	 * @param  [type] $accessToken [description]
	 * @param  [type] $instanceUrl [description]
	 * @param  [type] $data        [description]
	 * @return [type]              [description]
	 */
	public function createLead($accessToken, $instanceUrl, $data)
	{
        $instanceUrl = $instanceUrl.'/services/data/v39.0/sobjects/Lead/';

        $client = new Client();
        $lead = $client->request('POST', $instanceUrl, [
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
                'Authorization' => 'Bearer '.$accessToken,
                'Accept' => 'application/json',
                'Accept-Language' => 'en-US'
            ],
            'json' => $data
        ]);
        $response = json_decode($lead->getBody(), true);
        return $response;
	}

	/**
	 * update lead in salesforce
	 * @param  [type] $accessToken [description]
	 * @param  [type] $instanceUrl [description]
	 * @param  [type] $data        [description]
	 * @return [type]              [description]
	 */
	public function updateLead($accessToken, $leadId, $instanceUrl, $data)
	{
        $instanceUrl = $instanceUrl.'/services/data/v39.0/sobjects/Lead/'.$leadId;

        $client = new Client();
        $lead = $client->request('PATCH', $instanceUrl, [
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
                'Authorization' => 'Bearer '.$accessToken,
                'Accept' => 'application/json',
                'Accept-Language' => 'en-US'
            ],
            'json' => $data
        ]);
        $response = json_decode($lead->getBody(), true);
        return $response;
	}

	/**
	 * get lead in salesforce
	 * @param  [type] $accessToken [description]
	 * @param  [type] $instanceUrl [description]
	 * @param  [type] $data        [description]
	 * @return [type]              [description]
	 */
	public function getLead($accessToken, $leadId, $instanceUrl)
	{
        $instanceUrl = $instanceUrl.'/services/data/v39.0/sobjects/Lead/'.$leadId;

        $client = new Client();
        $lead = $client->request('GET', $instanceUrl, [
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
                'Authorization' => 'Bearer '.$accessToken,
                'Accept' => 'application/json',
                'Accept-Language' => 'en-US'
            ]
        ]);
        $response = json_decode($lead->getBody(), true);
        return $response;
	}
}