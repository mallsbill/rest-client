<?php
namespace Flex\RestClient;

/**
 * Rest Client Helper Response
 * @author clement
 */
class Response
{
    protected $body;
    protected $infos;
    protected $error;
    protected $headers;

    /**
     *
     * @param string $body response body from curl
     * @param array $infos response infos from curl
     * @param string $error request error
     */
    public function __construct($body, $infos, $error, $headers = array())
    {
        $this->body = $body;
        $this->infos = $infos;
        $this->error = $error;
        $this->headers = $headers;
    }

    /**
     * get a json object from response body
     * @param array[string]mixed $assoc force associative array for response
     * @throws JSonException
     * @return stdClass|array
     */
    public function getJsonDecode($assoc = false)
    {
        return json_decode($this->body, $assoc);
    }

    /**
     * get a simpleXMLElement object from response body
     * @param integer $options Libxml options
     * @return \SimpleXMLElement
     */
    public function getSimpleXml($options = LIBXML_NONET | LIBXML_ERR_WARNING)
    {
        $entity_loader = libxml_disable_entity_loader(true);
        $simpleXml = new \SimpleXMLElement($this->body, $options);
        libxml_disable_entity_loader($entity_loader);

        return $simpleXml;
    }

    /**
     * get reponse body str
     * @return string body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * get response info
     * @return array infos
     */
    public function getInfos()
    {
        return $this->infos;
    }
    
    /**
     * get response last error
     * @return string error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Return list of response headers
     * @return array headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Is http response information
     * @return boolean true for information, false for everything else
     */
    public function isInformation()
    {
        if ($this->infos['http_code'] < 100 || $this->infos['http_code'] >= 200) {
            return false;
        }
        return true;
    }

    /**
     * Is http request successful
     * @return boolean true for success, false for fail
     */
    public function isSuccessful()
    {
        if ($this->infos['http_code'] < 200 || $this->infos['http_code'] >= 300) {
            return false;
        }
        return true;
    }

    /**
     * is http response redirection
     * @return true for redirection, false for everything else
     */
    public function isRedirection()
    {
        if ($this->infos['http_code'] < 300 || $this->infos['http_code'] >= 400) {
            return false;
        }
        return true;
    }

    /**
     * is http reponse client error
     * @return boolean true for client error, false for everything else
     */
    public function isClientError()
    {
        if ($this->infos['http_code'] < 400 || $this->infos['http_code'] >= 500) {
            return false;
        }
        return true;
    }

    /**
     * is http reponse server errors
     * @return boolean true for server error, false for everything else
     */
    public function isServerError()
    {
        if ($this->infos['http_code'] < 500 || $this->infos['http_code'] >= 600) {
            return false;
        }
        return true;
    }

    /**
     * is http code ok
     * @return boolean
     */
    public function isOk()
    {
        return $this->infos['http_code'] == 200;
    }

    /**
     * is http code created
     * @return boolean
     */
    public function isCreated()
    {
        return $this->infos['http_code'] == 201;
    }

    /**
     * is http code created
     * @return boolean
     */
    public function isAccepted()
    {
        return $this->infos['http_code'] == 202;
    }

    /**
     * is http code bad request
     * @return boolean
     */
    public function isBadRequest()
    {
        return $this->infos['http_code'] == 400;
    }

    /**
     * is http code bad authentification
     * @return boolean
     */
    public function isBadAuthentification()
    {
        return $this->infos['http_code'] == 401;
    }

    /**
     * is http code forbidden
     * @return boolean
     */
    public function isForbidden()
    {
        return $this->infos['http_code'] == 403;
    }

    /**
     * is http code not found
     * @return boolean
     */
    public function isNotFound()
    {
        return $this->infos['http_code'] == 404;
    }

    /**
     * is http code not implemented
     * @return boolean
     */
    public function isNotImplemented()
    {
        return $this->infos['http_code'] == 501;
    }

    /**
     * get http result code
     * @return number
     */
    public function getHttpCode()
    {
        return $this->infos['http_code'];
    }

    /**
     * get http content type reponse header
     * @return type
     */
    public function getContentType()
    {
        return $this->infos['content_type'];
    }

    /**
     * check content type reponse header
     * @param type $ContentType content type to check
     * @return boolean true if content type is the same, false if not
     */
    public function checkContentType($ContentType)
    {
        if (strpos($this->infos['content_type'], $ContentType) === 0) {
            return true;
        }
        return false;
    }
}
