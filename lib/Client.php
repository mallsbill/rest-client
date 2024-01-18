<?php

namespace Flex\RestClient;

use InvalidArgumentException;
use LogicException;

/**
 * Send Rest request
 */
class Client
{
    // @property ressource $ch Curl Handler
    protected $ch;
    // @property ressource $fh File Handler
    protected $fh;
    protected $url;
    protected $method;
    protected $requestBody;
    protected $requestLength = 0;
    protected $headers = array();
    protected $sslVerify = false;
    protected $sslVersion = CURL_SSLVERSION_DEFAULT;
    protected $followLocation = true;
    protected $cookiePersistence = false;
    protected $cookieJarDirectory = '/tmp';
    protected $userAgent;
    protected $username;
    protected $password;
    protected $timeout = 5;
    protected $responseHeaders = array();
    protected $debug = false;
    protected $debugFile;

    /**
     * Contruct RestClient
     * @param string $url url to call
     * @param string $method http method
     * @param string|array $requestBody array of parameter or string to send
     */
    public function __construct($url = null, $method = Method::GET, $requestBody = null)
    {
        $this->url = $url;
        $this->method = $method;
        $this->requestBody = $requestBody;
        $this->headers['Accept'] = MineType::JSON;
    }

    /**
     * Init Curl handler and options
     * @throws InvalidArgumentException
     */
    public function init()
    {
        $this->ch = curl_init();

        switch (strtoupper($this->method)) {
            case Method::GET:
                $this->buildBody();
                $this->initGet();
                break;
            case Method::POST:
                $this->buildBody();
                $this->initPost();
                break;
            case Method::PUT:
                $this->buildBody();
                $this->initPut();
                break;
            case Method::PATCH:
                $this->buildBody();
                $this->initPatch();
                break;
            case Method::DELETE:
                $this->initDelete();
                break;
            default:
                throw new InvalidArgumentException('Current verb (' . $this->method . ') is an invalid REST method.');
        }

        $this->initCurlOpts();
        $this->initHeaders();
        $this->initAuth();
    }

    /**
     * Execute the request
     * @return Response
     */
    public function execute()
    {
        if ($this->debug === true) {
            $this->initDebug();
        }
        $this->init();
        return new Response(
            curl_exec($this->ch),
            curl_getinfo($this->ch),
            curl_error($this->ch),
            ResponseHeaders::get($this->ch),
            $this->debug === true ? $this->closeDebug() : null
        );
    }

    /**
     * Transform array of parameters to string
     */
    protected function buildBody()
    {
        if (is_array($this->requestBody)) {
            // if there's a file, don't transform to string
            foreach ($this->requestBody as $element) {
                if ($element instanceof \CURLFile) {
                    return;
                }
            }

            $this->requestBody = http_build_query($this->requestBody, '', '&');
        }
    }

    /**
     * Init Get Request
     */
    protected function initGet()
    {
        if (!empty($this->requestBody)) {
            $this->url .= (strpos($this->url, '?') === false) ? '?' . $this->requestBody : '&' . $this->requestBody;
        }
    }

    /**
     * Init Post Request
     */
    protected function initPost()
    {
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->requestBody);
    }

    /**
     * Init Put Request
     */
    protected function initPut()
    {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        if (!empty($this->requestBody)) {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->requestBody);
        }
    }

    /**
     * Init Patch Request
     */
    protected function initPatch()
    {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PATCH');

        if (!empty($this->requestBody)) {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->requestBody);
        }
    }

    /**
     * Init Delete Request
     */
    protected function initDelete()
    {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        if (!empty($this->requestBody)) {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->requestBody);
        }
    }

    /**
     * Define common curl options
     */
    protected function initCurlOpts()
    {
        if (empty($this->url)) {
            throw new LogicException('Url must be set');
        }

        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerify);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, $this->sslVerify);
        curl_setopt($this->ch, CURLOPT_SSLVERSION, $this->sslVersion);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, $this->followLocation);
        curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, '\Flex\RestClient\ResponseHeaders::callback');

        if (!empty($this->userAgent)) {
            curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent);
        }

        if ($this->cookiePersistence) {
            $cookie_jar = $this->getCookieJar();
            curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookie_jar);
            curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookie_jar);
        }

        if ($this->debug === true) {
            curl_setopt($this->ch, CURLOPT_STDERR, $this->debugFile);
            curl_setopt($this->ch, CURLOPT_VERBOSE, true);
        }
    }

    protected function initHeaders()
    {
        $headers = array();

        foreach ($this->headers as $name => $value) {
            $headers[] = $name . ': ' . $value;
        }

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Set Basic auth curl options
     */
    protected function initAuth()
    {
        if (!empty($this->username) && !empty($this->password)) {
            curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($this->ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        }
    }

    /**
     * Check if curl handler is init
     * @throws LogicException
     */
    protected function checkCurlHandler()
    {
        if ($this->ch instanceof \CurlHandle === false) {
            throw new LogicException('Curl handler not initialized');
        }
    }

    /**
     * Return curl ressource, work only from MultiClient
     * @return resource Curl
     * @throws LogicException
     */
    public function getCurlHandler()
    {
        $this->checkCurlHandler();
        return $this->ch;
    }

    public function getContentType()
    {
        return $this->headers['Content-Type'];
    }

    public function setContentType($contentType)
    {
        $this->headers['Content-Type'] = $contentType;
        return $this;
    }

    public function getAcceptType()
    {
        return $this->headers['Accept'];
    }

    public function setAcceptType($acceptType)
    {
        $this->headers['Accept'] = $acceptType;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setTimeout($timeout)
    {
        if (!is_numeric($timeout)) {
            throw new \InvalidArgumentException('timeout numeric expected');
        }
        $this->timeout = $timeout;
        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($name)
    {
        return $this->headers[$name];
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getSslVerify()
    {
        return $this->sslVerify;
    }

    public function setSslVerify($sslVerify)
    {
        if (is_bool($sslVerify) === false) {
            throw new \InvalidArgumentException('sslVerify boolean expected');
        }
        $this->sslVerify = $sslVerify;
        return $this;
    }

    public function getSslVersion()
    {
        return $this->sslVersion;
    }

    public function setSslVersion($sslVersion)
    {
        $this->sslVersion = $sslVersion;
        return $this;
    }

    public function getFollowLocation()
    {
        return $this->followLocation;
    }

    public function setFollowLocation($followLocation)
    {
        if (is_bool($followLocation) === false) {
            throw new \InvalidArgumentException('followLocation boolean expected');
        }
        $this->followLocation = $followLocation;
        return $this;
    }

    public function setRequestBody($requestBody)
    {
        $this->requestBody = $requestBody;
        return $this;
    }

    public function setRequestLength($requestLength)
    {
        $this->requestLength = $requestLength;
        return $this;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getCookiePersistence()
    {
        return $this->cookiePersistence;
    }

    public function setCookiePersistence($cookiePersistence)
    {
        if (is_bool($cookiePersistence) === false) {
            throw new \InvalidArgumentException('cookiePersistence boolean expected');
        }
        $this->cookiePersistence = $cookiePersistence;
        return $this;
    }

    public function getCookieJarDirectory()
    {
        return $this->cookieJarDirectory;
    }

    public function setCookieJarDirectory($cookieJarDirectory)
    {
        if (is_dir($cookieJarDirectory) === false) {
            throw new \LogicException($cookieJarDirectory . ' is not a valid directory');
        }

        $this->cookieJarDirectory = $cookieJarDirectory;
        return $this;
    }

    protected function getCookieJar()
    {
        if (empty($this->url)) {
            throw new \LogicException('url must be set');
        }

        $parts_url = parse_url($this->url);
        return $this->cookieJarDirectory . '/cookies_' . $parts_url['host'];
    }

    public function resetCookies()
    {
        if (file_exists($this->getCookieJar())) {
            unlink($this->getCookieJar());
        }
    }

    public function enableDebug()
    {
        $this->debug = true;
    }

    protected function initDebug()
    {
        $this->debugFile = tmpfile();
    }

    protected function closeDebug()
    {
        fseek($this->debugFile, 0);
        $content = fread($this->debugFile, 2048);
        fclose($this->debugFile);
        return $content;
    }

    /**
     * Close curl handler and file handler
     */
    public function __destruct()
    {
        if (!empty($this->fh)) {
            fclose($this->fh);
        }

        if (!empty($this->ch)) {
            curl_close($this->ch);
        }
    }
}
