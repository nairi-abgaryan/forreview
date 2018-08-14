<?php

namespace App\Service;

use App\Entity\User;
use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\Facebook;

class FacebookService
{
    /**
     * @var Facebook
     */
    private $facebookProvider;

    /**
     * @var Client
     */
    private $client;

    /**
     * FacebookDataFetcher constructor.
     *
     * @param Facebook $facebookProvider
     */
    public function __construct(Facebook $facebookProvider)
    {
        $this->facebookProvider = $facebookProvider;
        $this->client = new Client([
            'base_uri' => $facebookProvider::BASE_GRAPH_URL,
            'defaults' => ['headers' => [
                'content-type' => 'application/json',
                'Accept' => 'application/json',
            ]],
        ]);
    }

    /**
     * @param string $accessToken
     * @return string
     * @throws \League\OAuth2\Client\Provider\Exception\FacebookProviderException
     */
    public function getLongLivedAccessToken(string $accessToken): string
    {
        return $this->facebookProvider->getLongLivedAccessToken($accessToken)->getToken();
    }

    /**
     * @param string $accessToken
     *
     * @return array
     */
    public function getUserData(string $accessToken): array
    {
        return $this->getApiResults('/me', ['access_token' => $accessToken, 'redirect' => true,
            'fields' => 'id,first_name,last_name,email,
             picture.width(800)'
        ]);
    }


    /**
     * @param string $accessToken
     * @param User $user
     *
     * @return array
     */
    public function getLikesData(string $accessToken, User $user): array
    {
        $paginatedResult = $this->getApiResults('/' . $user->getFacebookId() . '', ['access_token' => $accessToken, 'redirect' => true,
            'fields' => 'likes{picture{height,is_silhouette,url,width},name}',
        ]);

        $output = $this->getDataFromPaginatedResult($paginatedResult['likes']);

        return $output;
    }


    /**
     * @param User $user
     *
     * @return array
     */
    public function getFriends(User $user): array
    {
        $paginatedResult = $this->getApiResults(sprintf('/%s/friends/', $user->getFacebookId()), [
            'access_token' => $user->getFacebookAccessToken(), 'fields' => 'id',
        ]);

        $output = $this->getDataFromPaginatedResult($paginatedResult);

        return $output;
    }

    /**
     * @param string $url
     * @param array $queryParams
     *
     * @return array
     */
    private function getApiResults(string $url, array $queryParams = []): array
    {
        return json_decode($this->client->get($url, ['query' => $queryParams])->getBody(), true);
    }

    /**
     * @param array $result
     *
     * @return array
     */
    private function getDataFromPaginatedResult(array $result): array
    {
        $data = [];

        while (true) {
            foreach ($result['data'] as $item) {
                array_push($data, $item);
            }

            if (!isset($result['paging']['next'])) {
               break;
            }

            $result = json_decode($this->client->get($result['paging']['next'])->getBody(), true);
        }

        return $data;
    }
}
