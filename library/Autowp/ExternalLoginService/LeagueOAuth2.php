<?php

use League;

abstract class Autowp_ExternalLoginService_LeagueOAuth2
    extends Autowp_ExternalLoginService_Abstract
{
    /**
     * @var League\OAuth2\Client\Provider\AbstractProvider
     */
    protected $_provider;

    /**
     * @var string
     */
    protected $_accessToken;

    /**
     * @return League\OAuth2\Client\Provider\AbstractProvider
     */
    abstract protected function _createProvider();

    /**
     * @return League\OAuth2\Client\Provider\AbstractProvider
     */
    protected function _getProvider()
    {
        if (!$this->_provider) {
            $this->_provider = $this->_createProvider();
        }

        return $this->_provider;
    }

    /**
     * @return string
     */
    abstract protected function _getAuthorizationUrl();

    /**
     * @return string
     */
    abstract protected function _getFriendsAuthorizationUrl();

    public function getState()
    {
        return $this->_getProvider()->getState();
    }

    public function getLoginUrl()
    {
        return $this->_getAuthorizationUrl();
    }

    public function getFriendsUrl()
    {
        return $this->_getFriendsAuthorizationUrl();
    }

    public function callback(array $params)
    {
        $provider = $this->_getProvider();

        $this->_accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $params['code']
        ]);

        return $this->_accessToken;
    }
}