<?php

namespace Flex\RestClient;

Class ResponseCollection extends AbstractCollection {

	/**
	 * Sets a Response in the collection at the specified key/index.
	 *
	 * @param \Flex\RestClient\Response $response Response
	 */
	public function add(Response $response) {
		parent::add($response);
	}

	/**
	 * Sets a Response in the collection at the specified key/index.
	 *
	 * @param string|integer $key   The key/index of the element to set.
	 * @param \Flex\RestClient\Response $response Response
	 */
	public function set($key, Response $response) {
		parent::set($key, $response);
	}

	/**
	 * Gets the element at the specified key/index.
	 *
	 * @param string|integer $key The key/index of the element to retrieve.
	 *
	 * @return \Flex\RestClient\Response
	 */
	public function get($key) {
		return parent::get($key);
	}

}
