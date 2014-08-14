<?php
namespace Flex\RestClient;

/**
 * Send parallel Rest requests
 */
Class ClientCollection extends AbstractCollection {

	// @property ressource $mh Curl Multi Handler
	protected $mh;

	/**
     * Sets a Client in the client collection at the specified key/index.
     *
     * @param string|integer $key   The key/index of the element to set.
     * @param \Flex\RestClient\Client $client Client
     */
	public function add(Client $client) {
		$this->collection[] = $client;
	}

	/**
     * Sets a Client in the collection at the specified key/index.
     *
     * @param string|integer $key   The key/index of the element to set.
     * @param \Flex\RestClient\Client $client Client
     */
	public function set($key, Client $client) {
		$this->collection[$key] = $client;
	}

	/**
	 * init Rest multi client from Client instance
	 */
	protected function init() {

		$this->mh = curl_multi_init();

		foreach($this->collection as $key => $client){
			$client->init();
			curl_multi_add_handle($this->mh, $client->getCurlHandler());
		}

	}

	public function execute(){

		$this->init();

		$active = null;
		$selectTimeout = 0.001;
			
		//execute the handles
		do {
			$status = curl_multi_exec($this->mh, $active);

			if ($active && curl_multi_select($this->mh, $selectTimeout) === -1) {
                // Perform a usleep if a select returns -1: https://bugs.php.net/bug.php?id=61141
                usleep(150);
            }
		} while ($status === CURLM_CALL_MULTI_PERFORM || $active);

		$responses = new ResponseCollection();

		foreach($this->collection as $key => $client){
			$responses[$key] = new Response(curl_multi_getcontent($client->getCurlHandler()),
											curl_getinfo($client->getCurlHandler()),
											curl_error($client->getCurlHandler()));
		}

		return $responses;
		
	}

	//close the handles
	public function __destruct() {

		if(empty($this->mh))
			return;
		
		foreach($this->collection as $key => $client){
			curl_multi_remove_handle($this->mh, $client->getCurlHandler());
		}

		curl_multi_close($this->mh);
	}

}
