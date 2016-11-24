<?php

namespace Martsins\UpMonitBundle\Services\Helper;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

class UpMonitHelper
{
    const UP_MONIT_TOKEN_REGEX = '/^(?:(.+):)?\/\/(?:(.+)(:.+)?@)?([\w\.-]+)(?::(\d+))?(\/.*)/i';

    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Check UpMonit token
     *
     * @return bool
     */
    public function checkToken() {
        $return = FALSE;
        preg_match(self::UP_MONIT_TOKEN_REGEX, $this->token, $match);
        if (isset($match[1]) && isset($match[2]) && isset($match[4]) && isset($match[6])) {
            $hash_token = md5($match[2]);
            $url = $match[1] . '://' . $match[4] . '/api/token/check' . $match[6] . '/' . $hash_token;
        }

        if (isset($url) && !empty($url) && isset($hash_token)) {
            $client = new Client();
            try {
                $response = $client->post($url)->send();
                $body = json_decode($response->getBody());
                if ($response->getStatusCode() == '201' && !empty($body)) {
                    $return = ($body == $hash_token) ? TRUE : $return;
                }
            } catch (ClientErrorResponseException $e) {
                //ToDo: errors
            }
        }

        return $return;
    }

    /**
     * Send data to UpMonit server
     *
     * @param $data
     */
    public function sendData($data) {
        preg_match(self::UP_MONIT_TOKEN_REGEX, $this->token, $match);
        if (isset($match[1]) && isset($match[2]) && isset($match[4]) && isset($match[6])) {
            $link = $match[1] . '://' . $match[4] . '/api/project' . $match[6] . '/' . $match[2];
        }

        if (isset($link) && !empty($link)) {
            $client = new Client();
            try {
                $client->post(
                  $link,
                  ['Content-Type' => 'application/json'],
                  json_encode($data)
                )->send();
            } catch (ClientErrorResponseException $e) {
                //ToDo: errors
            }
        }
    }

}