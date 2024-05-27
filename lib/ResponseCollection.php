<?php

namespace Pephpit\RestClient;

class ResponseCollection extends AbstractCollection
{
    /**
     * Sets a Response in the collection at the specified key/index.
     *
     * @param object $response Response
     */
    public function add($response)
    {
        if ($response instanceof Response === false) {
            throw new \InvalidArgumentException('$response must be an instance of \Pephpit\RestClient\Response');
        }

        parent::add($response);
    }

    /**
     * Sets a Response in the collection at the specified key/index.
     *
     * @param string|integer $key   The key/index of the element to set.
     * @param object $response Response
     */
    public function set($key, $response)
    {
        if ($response instanceof Response === false) {
            throw new \InvalidArgumentException('$response must be an instance of \Pephpit\RestClient\Response');
        }

        parent::set($key, $response);
    }

    /**
     * Gets the element at the specified key/index.
     *
     * @param string|integer $key The key/index of the element to retrieve.
     *
     * @return \Pephpit\RestClient\Response
     */
    public function get($key)
    {
        return parent::get($key);
    }

    /**
     * Is all http requests are successful
     * @return boolean true for success, false for fail
     */
    public function isSuccessful()
    {
        foreach ($this->collection as $response) {
            if ($response->isSuccessful() === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return infos of all requests
     *
     * @return array
     */
    public function getInfos()
    {
        $infos = [];
        /* @var $response Response */
        foreach ($this->collection as $key => $response) {
            $infos[$key] = $response->getInfos();
        }

        return $infos;
    }
}
