<?php

abstract class Autowp_ExternalLoginService_OAuth
    extends Autowp_ExternalLoginService_Abstract
{
    /**
     * @return Zend_Session_Namespace
     */
    protected function _getOauthSession()
    {
        return new Zend_Session_Namespace('Oauth2');
    }

    /**
     * @return Autowp_Oauth2_Client
     */
    protected function _getOauth()
    {
        if (!isset($this->_options['oauthOptions'])) {
            throw new Autowp_ExternalLoginService_Exception(
                "'oauthOptions' not found"
            );
        }

        $oauth2 = new Autowp_Oauth2_Client($this->_options['oauthOptions']);
        $oauth2->setSession($this->_getOauthSession());

        return $oauth2;
    }

    /**
     * @param array $options
     * @return string
     */
    public function getLoginUrl(array $options)
    {
        return $this->_getOauth()->getLoginUrl(array(
            'redirect_uri' => $options['redirect_uri']
        ));
    }

    /**
     * @param array $params
     */
    public function callback(array $params)
    {
        $data = array();
        $accessToken = $this->_getOauth()->getAccessToken($params, $data);

        return $this->_processCallback($accessToken, $data);
    }

    abstract public function _processCallback($accessToken, $data);

    /**
     *
     * @param string $url
     * @param array $params
     * @throws Exception
     * @return mixed
     */
    protected function _genericApiCall($url, array $params)
    {
        $client = new Zend_Http_Client($url);
        $client->setParameterGet($params);
        $response = $client->request();
        if (!$response->isSuccessful()) {

            $body = $response->getBody();

            $httpErrorMessage = 'HTTP ' . $response->getStatus() . ' ' . $response->getMessage();
            $oauth2ErrorMessage = false;
            try {
                $json = Zend_Json::decode($body);
                $oauth2ErrorMessage = isset($json['error']) ? $json['error'] : null;
            } catch (Zend_Json_Exception $e) {

            }
            $errorMessage = $httpErrorMessage;
            if ($oauth2ErrorMessage) {
                $errorMessage .= ' (' . print_r($oauth2ErrorMessage, true) . ')';
            }
            throw new Autowp_ExternalLoginService_Exception($errorMessage);
        }

        $body = $response->getBody();
        $json = Zend_Json::decode($body);

        if (isset($json['error']) && $json['error']) {
            $errorMessage = $json['error'];
            if (isset($json['error_description']) && $json['error_description']) {
                $errorMessage .= ' (' . $json['error_description'] . ')';
            }
            throw new Autowp_ExternalLoginService($errorMessage);
        }

        return $json;
    }
}