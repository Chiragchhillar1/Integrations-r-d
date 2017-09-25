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

    /**
     * Get users from servicenow
     * @return [type] [description]
     */
      public function fetchUsers($accessToken)
      {
        $client = new Client();
        $users = $client->request('GET', 'https://dev34876.service-now.com/api/now/table/sys_user', [
            'headers' => [
                'Accept'    => 'application/json',
                'Authorization' => 'Bearer '.$accessToken
            ]
        ]);
        $response = json_decode($users->getBody(), true);
        return $response;
      }

    /**
     * Get usersGroup from servicenow
     * @return [type] [description]
     */
      public function fetchUsersGroup($accessToken)
      {
        $client = new Client();
          $groups = $client->request('GET', 'https://dev34876.service-now.com/api/now/table/sys_user_group', [
              'headers' => [
                  'Accept'    => 'application/json',
                  'Authorization' => 'Bearer '.$accessToken
              ]
          ]);
          $response = json_decode($groups->getBody(), true);
          return $response;
      }

    /**
     * create problem in servicenow incomplete
     * @return [type] [description]
     */
      public function createProblem($accessToken, $data)
      {
        $client = new Client();

        $problem = $client->request('POST', 'https://dev34876.service-now.com/api/now/table/problem', [
           'headers' => [
               'Authorization' => 'Bearer ' . $accessToken,
               'Content-Type' => 'application/json'
           ],
           'json' => $data
        ]);

        $problem = json_decode($problem->getBody(), true);
        return $problem;
      }

    /**
     * Get problems in servicenow
     * @return [type] [description]
     */
      public function fetchProblems($accessToken)
      {
        $client = new Client();
          $problems = $client->request('GET', 'https://dev34876.service-now.com/api/now/table/problem', [
              'headers' => [
                  'Accept'    => 'application/json',
                  'Authorization' => 'Bearer '.$accessToken
              ]
          ]);
          $response = json_decode($problems->getBody(), true);
          return $response;
      }

      /**
       * Get problem in servicenow
       * @return [type] [description]
       */
        public function fetchProblem($accessToken, $problemId)
        {
          $client = new Client();
            $problems = $client->request('GET', 'https://dev34876.service-now.com/api/now/table/problem/'.$problemId, [
                'headers' => [
                    'Accept'    => 'application/json',
                    'Authorization' => 'Bearer '.$accessToken
                ]
            ]);
            $response = json_decode($problems->getBody(), true);
            return $response;
        }

    /**
     * create problem in servicenow incomplete
     * @return [type] [description]
     */
      public function updateProblem($accessToken, $problemId, $data)
      {
        $client = new Client();

        $updatedProblem = $client->request('PUT', 'https://dev34876.service-now.com/api/now/table/problem/'.$problemId, [
           'headers' => [
               'Authorization' => 'Bearer ' . $accessToken,
               'Content-Type' => 'application/json'
           ],
           'json' => $data
        ]);

        $updatedProblem = json_decode($updatedProblem->getBody(), true);
        return $updatedProblem;
      }
}