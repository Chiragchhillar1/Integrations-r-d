<?php

namespace ChiragChhillar\Helpdesk\Freshdesk;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;

class Manager
{
	/**
	 *  Create ticket in freshdesk
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function createTicket($domainName, $apiKey, $data)
	{
		$client = new Client();
        $ticket = $client->request('POST', 'https://'.$domainName.'.freshdesk.com/api/v2/tickets', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $apiKey
            ],
            'json' => $data
        ]);
        $response = json_decode($ticket->getBody(), true);
        return $response;
	}

	/**
	 *  Update ticket in freshdesk
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function updateTicket($domainName, $apiKey, $ticketId, $data)
	{
		$client = new Client();
        $ticket = $client->request('PUT', 'https://'.$domainName.'.freshdesk.com/api/v2/tickets/'.$ticketId, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $apiKey
            ],
            'json' => $data
        ]);
        $response = json_decode($ticket->getBody(), true);
        return $response;
	}

	/**
	 *  Get a ticket from freshdesk
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function getTicket($domainName, $apiKey,  $ticketId)
	{
		$client = new Client();
        $ticket = $client->request('GET', 'https://'.$domainName.'.freshdesk.com/api/v2/tickets/'.$ticketId, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $apiKey
            ]
        ]);
        $response = json_decode($ticket->getBody(), true);
        return $response;
	}

	/**
	 *  Get agents from freshdesk
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function getAgents($domainName, $apiKey)
	{
		$client = new Client();
        $ticket = $client->request('GET', 'https://'.$domainName.'.freshdesk.com/api/v2/agents', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $apiKey
            ]
        ]);
        $response = json_decode($ticket->getBody(), true);
        return $response;
	}

	/**
	 *  Get a info about contact from freshdesk
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function getContact($domainName, $apiKey, $contactId)
	{
		$client = new Client();
        $contact = $client->request('GET', 'https://'.$domainName.'.freshdesk.com/api/v2/contacts/'.$contactId, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $apiKey
            ]
        ]);
        $response = json_decode($contact->getBody(), true);
        return $response;
	}

	/**
	 *  Fetch KB categories
	 * @return [type] [description]
	 */
	public function categories($domainName, $apiKey)
	{
		$client = new Client();
        $ticket = $client->request('GET', 'https://'.$domainName.'.freshdesk.com/api/v2/solutions/categories', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $apiKey
            ]
        ]);
        $response = json_decode($ticket->getBody(), true);
        return $response;
	}

	/**
	 *  Fetch KB folders
	 * @return [type] [description]
	 */
	public function folders($domainName, $apiKey, $categoryId)
	{
		$client = new Client();
        $ticket = $client->request('GET', 'https://'.$domainName.'.freshdesk.com/api/v2/solutions/categories/'.$categoryId.'/folders', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $apiKey
            ]
        ]);
        $response = json_decode($ticket->getBody(), true);
        return $response;
	}

	/**
	 *  Fetch KB articles
	 * @return [type] [description]
	 */
	public function articles($domainName, $apiKey, $folderId)
	{
		$client = new Client();
        $ticket = $client->request('GET', 'https://'.$domainName.'.freshdesk.com/api/v2/solutions/folders/'.$folderId.'/articles', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $apiKey
            ]
        ]);
        $response = json_decode($ticket->getBody(), true);
        return $response;
	}
}