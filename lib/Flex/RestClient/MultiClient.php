<?php
namespace Flex\RestClient;

use InvalidArgumentException;

/**
 * Send parallel Rest requests
 */
Class MultiClient {

	// @property ressource $mh Curl Multi Handler
	protected $mh;
	protected $restclient_array;

	/**
	 * Contruct Rest multi client from RestClient instance
	 * @param array $restclient_array array of RestClient instance
	 * @throws InvalidArgumentException
	 */
	public function __construct(array $restclient_array) {

		$this->mh = curl_multi_init();

		foreach($restclient_array as $key => $restclient){
			if($restclient instanceof Client){
				$this->restclient_array[$key] = $restclient;
				$restclient->init();
				curl_multi_add_handle($this->mh, $restclient->getCurlHandler());
			}
			else {
				throw new InvalidArgumentException('Each element of the array must be an instance of \Core\Lib\Contact\RestClient');
			}
		}

	}

	public function execute(){

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

		foreach($this->restclient_array as $key => $restclient){
			$restclient->setResponseBody();
			$restclient->setResponseInfo();
			$restclient->setResponseError();
		}

		return $this->restclient_array;
		
	}

	//close the handles
	public function __destruct() {
		foreach($this->restclient_array as $key => $restclient){
			curl_multi_remove_handle($this->mh, $restclient->getCurlHandler());
		}

		curl_multi_close($this->mh);
	}

}
