<?php

namespace Flex\RestClient;

Class ResponseCollection extends AbstractCollection {

	/**
	 * Sets a Response in the collection at the specified key/index.
	 *
	 * @param \Flex\RestClient\Response $response Response
	 */
	public function add(Response $response) {
		$this->collection[] = $response;
	}

	/**
	 * Sets a Response in the collection at the specified key/index.
	 *
	 * @param string|integer $key   The key/index of the element to set.
	 * @param \Flex\RestClient\Response $response Response
	 */
	public function set($key, Response $response) {
		$this->collection[$key] = $response;
	}

}
