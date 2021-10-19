<?php

namespace RubenBuijs\MailcoachApiWrapper;

use GuzzleHttp\Client;
use RubenBuijs\MailcoachApiWrapper\Exceptions\InvalidSubscriber;
use RubenBuijs\MailcoachApiWrapper\Exceptions\ProcessingError;

class Newsletter
{
    private string $api_token;
    private string $api_url;
    private int $list_id;
    private bool $use_ssl;

    const TIMEOUT = 10;

    /**
     * Newsletter constructor.
     *
     * @param string $api_token
     * @param string $api_url
     * @param int    $list_id
     * @param bool   $use_ssl
     * @throws ProcessingError
     */
    public function __construct(string $api_token, string $api_url, int $list_id, bool $use_ssl)
    {
        $this->api_token = $api_token;
        $this->api_url = $api_url;
        $this->list_id = $list_id;
        $this->use_ssl = $use_ssl;

        if(is_null($this->api_token) || is_null($this->api_url) || is_null($this->list_id)) {
            throw ProcessingError::invalidCredentials();
        }

    }

    /**
     * Subscribe a person to the list
     *
     * @param string $email
     * @param string $name
     * @param array  $tags
     * @return array|false
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function subscribe(string $email, string $name, array $tags = [])
    {
        return $this->post('/email-lists/'.$this->list_id.'/subscribers', [
            'email' => $email,
            'first_name' => $name,
            'tags' => $tags
        ]);
    }

    /**
     * Add tags to a subscriber
     *
     * @param string $email
     * @param array  $tags_to_add
     * @return array|false
     * @throws InvalidSubscriber
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addTags(string $email, array $tags_to_add)
    {
        $subscriber = $this->getSubscriberByEmail($email);
        $existing_tags = $subscriber->tags;

        // Merge and only keep unique tags
        $new_tags = array_unique(array_merge($tags_to_add, $existing_tags));

        return $this->patch('/subscribers/' . $subscriber->id, [
            'tags' => $new_tags
        ]);
    }

    /**
     * Delete specific tags from a subscriber
     *
     * @param string $email
     * @param array  $tags_to_delete
     * @return array|false
     * @throws InvalidSubscriber
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteTags(string $email, array $tags_to_delete)
    {
        $subscriber = $this->getSubscriberByEmail($email);
        $existing_tags = $subscriber->tags;
        $new_tags = [];

        // Remove tags that are in both arrays
        foreach ($existing_tags as $tag) {
            if(!in_array($tag, $tags_to_delete)) {
                $new_tags[] = $tag;
            }
        }

        return $this->patch('/subscribers/' . $subscriber->id, [
            'tags' => $new_tags
        ]);
    }

    /**
     * Update the subscriber. Enter NULL when data should not be changed.
     *
     * @param string      $email
     * @param string|null $new_email
     * @param string|null $name
     * @param array|null  $tags
     * @return array|false
     * @throws InvalidSubscriber
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(string $email, string $new_email = null, string $name = null, array $tags = null)
    {
        $subscriber = $this->getSubscriberByEmail($email);

        $patch = [];
        if(!is_null($new_email)) $patch['email'] = $new_email;
        if(!is_null($name)) $patch['first_name'] = $name;
        if(!is_null($tags)) {
            $patch['tags'] = $tags;
        } else {
            $patch['tags'] = $subscriber->tags; // otherwise all tags will be removed
        }

        return $this->patch('/subscribers/' . $subscriber->id, $patch);
    }

    /**
     * Retrieve subscriber by email
     *
     * @param string $email
     *
     * @return mixed
     * @throws InvalidSubscriber
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSubscriberByEmail(string $email)
    {
        $response = $this->get('/email-lists/'.$this->list_id.'/subscribers', [
            'filter[search]' => $email
        ]);
        if(count($response->data) == 0) {
            throw InvalidSubscriber::noSubscriberFound($email);
        }
        if(count($response->data) != 1) {
            throw InvalidSubscriber::multipleSubscribersFound($email);
        }
        return $response->data[0];
    }



    /**
     * Make an HTTP DELETE request - for deleting data
     *
     * @param   string $url  URL of the API request method
     * @param   array  $params    Assoc array of arguments (if any)
     * @param   int    $timeout Timeout limit for request in seconds
     *
     * @return  array|false   Assoc array of API response, decoded from JSON
     */
    public function delete(string $url, array $params = [], $timeout = self::TIMEOUT)
    {
        return $this->makeRequest('delete', $url, $params, $timeout);
    }

    /**
     * Make an HTTP GET request - for retrieving data
     *
     * @param string $url     URL of the API request method
     * @param array  $params  Assoc array of arguments (usually your data)
     * @param int    $timeout Timeout limit for request in seconds
     *
     * @return  array|false   Assoc array of API response, decoded from JSON
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $url, array $params = [], $timeout = self::TIMEOUT)
    {
        return $this->makeRequest('get', $url, $params, $timeout);
    }

    /**
     * Make an HTTP PATCH request - for performing partial updates
     *
     * @param string $url     URL of the API request method
     * @param array  $params  Assoc array of arguments (usually your data)
     * @param int    $timeout Timeout limit for request in seconds
     *
     * @return  array|false   Assoc array of API response, decoded from JSON
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function patch(string $url, array $params = [], $timeout = self::TIMEOUT)
    {
        return $this->makeRequest('patch', $url, $params, $timeout);
    }

    /**
     * Make an HTTP POST request - for creating and updating items
     *
     * @param string $url     URL of the API request method
     * @param array  $params  Assoc array of arguments (usually your data)
     * @param int    $timeout Timeout limit for request in seconds
     *
     * @return  array|false   Assoc array of API response, decoded from JSON
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(string $url, array $params = [], $timeout = self::TIMEOUT)
    {
        return $this->makeRequest('post', $url, $params, $timeout);
    }

    /**
     * Make an HTTP PUT request - for creating new items
     *
     * @param string $url     URL of the API request method
     * @param array  $params  Assoc array of arguments (usually your data)
     * @param int    $timeout Timeout limit for request in seconds
     *
     * @return  array|false   Assoc array of API response, decoded from JSON
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put(string $url, array $params = [], $timeout = self::TIMEOUT)
    {
        return $this->makeRequest('put', $url, $params, $timeout);
    }

    /**
     * Performs the underlying HTTP request. Not very exciting.
     *
     * @param string $http_verb The HTTP verb to use: get, post, put, patch, delete
     * @param string $url
     * @param array  $params    Assoc array of parameters to be passed
     * @param int    $timeout
     *
     * @return array|false Assoc array of decoded result
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function makeRequest(string $http_verb, string $url, array $params = [], $timeout = self::TIMEOUT)
    {
        $client = new Client([
            'timeout' => $timeout,
            'verify' => $this->use_ssl
        ]);

        $headers = [
            'Authorization' => 'Bearer ' . $this->api_token,
            'Accept'        => 'application/json',
        ];

        $full_url = $this->api_url . $url;
        $response = $client->request($http_verb, $full_url, ['query' => $params, 'headers' => $headers]);

        return json_decode($response->getBody());
    }
}