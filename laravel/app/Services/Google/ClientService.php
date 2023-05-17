<?php

namespace App\Services\Google;

use App\Models\AuthInfo;
use Google\Client;
use Google\Service\Calendar;

class ClientService
{
	protected $client;

	const AUTH_INFO_TARGET_ID = 1;

	public function __construct()
	{
		$this->setClient();
		$this->setAccessToken();
	}

	private function setClient()
	{
		$config = config("googleClient.account");

		$this->client = new Client();
		$this->client->setAuthConfig($config);
		$this->client->addScope(Calendar::CALENDAR);
		$this->client->setAccessType('offline');
		$this->client->setApprovalPrompt('consent');
		$this->client->setIncludeGrantedScopes(true);
	}

	public function getClient()
	{
		return $this->client;
	}

	private function setAccessToken()
	{
		$authInfo = AuthInfo::select("access_token", "refresh_token")
		->where("id", self::AUTH_INFO_TARGET_ID)
		->first()
		->toArray();

		$this->client->setAccessToken($authInfo["access_token"]);

        if (!$this->client->isAccessTokenExpired()) {
            return;
        }

        $creds = $this->client->refreshToken($authInfo["refresh_token"]);

        AuthInfo::where("id", self::AUTH_INFO_TARGET_ID)
        ->update(["access_token" => $creds["access_token"]]);
	}
}
