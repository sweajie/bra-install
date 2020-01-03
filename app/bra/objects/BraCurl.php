<?php

namespace app\bra\objects;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class BraCurl
{
    /**
     * @var Client
     */
    public $client;
    public $base_uri;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param $url
     * @param string $method
     * @param array $data
     * @return \Psr\Http\Message\StreamInterface  | \Psr\Http\Message\MessageInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetch($url, $method = 'GET', $data = [])
    {
        $response = $this->client->request($method, $url, $data);
        return $response;
    }

    public function fetch_ansyc($url, $method = 'GET', $data = [])
    {
        $promise = $this->client->requestAsync($method, $url);
        $promise->then(
            function (ResponseInterface $res) {
                echo $res->getStatusCode() . "\n";
            },
            function (RequestException $e) {
                echo $e->getMessage() . "\n";
                echo $e->getRequest()->getMethod();
            }
        );
    }

    /**
     * @param $url
     * @param string $method
     * @param array $data
     * @return \Psr\Http\Message\StreamInterface | \Psr\Http\Message\MessageInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function test_url($url, $method = 'GET', $data = [])
    {
        $response = $this->fetch($url, $method, $data);
        return $response;
    }

    public function get_content($url, $method = 'GET', $data = [], $format = true)
    {
        try {
            $response = $this->fetch($url, $method, $data);
        } catch (RequestException $e) {
            echo $e->getRequest();
            echo ( $e->getResponse()->getBody());
            dd(5);
        }
        $content = $response->getBody();
        if ($format) {
            $content = json_decode($content, 1);
        }
        return $content;
    }
}