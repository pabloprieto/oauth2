<?php

namespace OAuth2;

use Zend\Http;

class Client
{

    const GRANT_TYPE_AUTH_CODE          = 'authorization_code';
    const GRANT_TYPE_PASSWORD           = 'password';
    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';
    const GRANT_TYPE_REFRESH_TOKEN      = 'refresh_token';

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var Http\Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $accessTokenParamName = 'access_token';

    /**
     * @param string $key
     * @param string $secret
     */
    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * @param  Http\Client $httpClient
     * @return $this
     */
    public function setHttpClient(Http\Client $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * @return Http\Client
     */
    public function getHttpClient()
    {
        if (null === $this->httpClient) {
            $this->setHttpClient(new Http\Client());
        }

        return $this->httpClient;
    }

    /**
     * @param  string $name
     * @return $this
     */
    public function setAccessTokenParamName($name)
    {
        $this->accessTokenParamName = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccessTokenParamName()
    {
        return $this->accessTokenParamName;
    }

    /**
     * @param  string $token
     * @return $this
     */
    public function setAccessToken($token)
    {
        $this->accessToken = $token;

        return $this;
    }

    /**
     * @param  string $uri
     * @param  string $grant_type
     * @param  array  $params
     * @return string
     */
    public function getAccessToken($uri, $grant_type, $params = array())
    {
        $params = array_merge($params, array(
            'grant_type'    => $grant_type,
            'client_id'     => $this->key,
            'client_secret' => $this->secret
        ));

        $httpClient = $this->getHttpClient();
        $httpClient->setUri($uri)->setMethod('POST')->setParameterPost($params);

        $response = $httpClient->send();

        return $response->getBody();
    }

    /**
     * @param  string $uri
     * @param  array  $params
     * @return string
     */
    public function getAuthenticationUrl($uri, $params = array())
    {
        $params = array_merge($params, array(
            'response_type' => 'code',
            'client_id'     => $this->key
        ));

        return $uri . '?' . http_build_query($params);
    }

    /**
     * @param  string|\Zend\Uri\Http $uri
     * @param  array                 $params
     * @param  string                $method
     * @param  string                $body
     * @return Http\Response
     */
    public function fetch($uri, $params = array(), $method = 'GET', $body = '')
    {
        if ($this->accessToken) {
            $params = array_merge($params, array(
                $this->accessTokenParamName => $this->accessToken
            ));
        }

        $httpClient = $this->getHttpClient()->resetParameters();
        $httpClient->setUri($uri)->setMethod($method)->setParameterGet($params)->setRawBody($body);

        return $httpClient->send();
    }

}
