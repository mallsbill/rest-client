<?php
namespace Flex\RestClient;

use InvalidArgumentException;
use LogicException;

/**
 * Send Rest request
 */
Class Client
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

	protected $username;
	protected $password;
	
	protected $timeout = 5;

	/**
	 * Contruct RestClient
	 * @param string $url url to call
	 * @param string $method http method
	 * @param various $requestBody array of parameter or string to send
	 */
	public function __construct($url = null, $method = Method::GET, $requestBody = null) {
		$this->url				= $url;
		$this->method			= $method;
		$this->requestBody		= $requestBody;
		$this->headers['Accept']= MineType::JSON;
	}

	/**
	 * Init Curl handler and options
	 * @throws InvalidArgumentException
	 */
	public function init() {
		$this->ch = curl_init();

		$this->buildPostBody();

		switch (strtoupper($this->method))
		{
			case Method::GET:
				$this->initGet();
				break;
			case Method::POST:
				$this->initPost();
				break;
			case Method::PUT:
				$this->initPut();
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
	 * Return Flex\RestClient\Response
	 */
	public function execute() {

		$this->init();
		return new Response(curl_exec($this->ch),
							curl_getinfo($this->ch),
							curl_error($this->ch));

	}

	/**
	 * Transform array of parameters to string
	 * @param array $data
	 */
	public function buildPostBody($data = null) {
		$data = ($data !== null) ? $data : $this->requestBody;

		if ( is_array($data) )
		{
			$data = http_build_query($data, '', '&');
		}
		
		$this->requestBody = $data;
	}

	/**
	 * Init Get Request
	 */
	protected function initGet() {
		if(!empty($this->requestBody))
			$this->url .= ( strpos($this->url, '?') === false ) ? '?'.$this->requestBody : '&'.$this->requestBody;
	}

	/**
	 * Init Post Request
	 */
	protected function initPost() {

		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->requestBody);
		curl_setopt($this->ch, CURLOPT_POST, true);

	}

	/**
	 * Init Put Request
	 */
	protected function initPut() {

		$this->requestLength = strlen($this->requestBody);

		$this->fh = fopen('php://memory', 'rw');
		fwrite($this->fh, $this->requestBody);
		rewind($this->fh);

		curl_setopt($this->ch, CURLOPT_INFILE, $this->fh);
		curl_setopt($this->ch, CURLOPT_INFILESIZE, $this->requestLength);
		curl_setopt($this->ch, CURLOPT_PUT, true);

	}

	/**
	 * Init Delete Request
	 */
	protected function initDelete() {
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

		if( !empty($this->requestBody) ){
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->requestBody);
			curl_setopt($this->ch, CURLOPT_POST, true);
		}

	}

	/**
	 * Define common curl options
	 */
	protected function initCurlOpts() {

		if(empty($this->url)){
			throw new LogicException('Url must be set');
		}

		curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($this->ch, CURLOPT_URL, $this->url);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerify);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, $this->sslVerify);
	}

	protected function initHeaders() {

		$headers = array();

		foreach ($this->headers as $name => $value) {
			$headers[] = $name.': '.$value;
		}

		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers );
	}

	/**
	 * Set Basic auth curl options
	 */
	protected function initAuth() {
		if ( !empty($this->username) && !empty($this->password) ) {
			curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($this->ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		}
	}

	/**
	 * Check if curl handler is init
	 * @throws LogicException
	 */
	protected function checkCurlHandler() {
		if( is_resource($this->ch) != 'curl' ){
			throw new LogicException('Curl handler not initialized');
		}
	}

	/**
	 * Return curl ressource, work only from MultiClient
	 * @return ressource Curl
	 * @throws LogicException
	 */
	public function getCurlHandler(){
		$this->checkCurlHandler();
		return $this->ch;
	}

	public function getContentType() {
		return $this->headers['Content-Type'];
	}

	public function setContentType($contentType) {
		$this->headers['Content-Type'] = $contentType;
		return $this;
	}

	public function getAcceptType() {
		return $this->headers['Accept'];
	}

	public function setAcceptType($acceptType) {
		$this->headers['Accept'] = $acceptType;
		return $this;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}

	public function getMethod() {
		return $this->method;
	}

	public function setMethod($method) {
		$this->method = $method;
		return $this;
	}

	public function getTimeout() {
		return $this->timeout;
	}

	public function setTimeout($timeout) {
		if(!is_numeric($timeout)) {
			throw new \InvalidArgumentException('timeout numeric expected');
		}
		$this->timeout = $timeout;
		return $this;
	}
	
	public function getHeaders() {
		return $this->headers;
	}

	public function getHeader($name) {
		return $this->headers[$name];
	}

	public function setHeader($name, $value) {
		$this->headers[$name] = $value;
		return $this;
	}

	public function getSslVerify() {
		return $this->sslVerify;
	}

	public function setSslVerify($sslVerify) {
		if(is_bool($sslVerify)) {
			throw new \InvalidArgumentException('sslVerify boolean expected');
		}
		$this->sslVerify = $sslVerify;
		return $this;
	}

	public function setRequestBody($requestBody) {
		$this->requestBody = $requestBody;
		return $this;
	}

	public function setRequestLength($requestLength) {
		$this->requestLength = $requestLength;
		return $this;
	}

	/**
	 * Close curl handler and file handler
	 */
	public function __destruct() {
		
		if( !empty($this->fh) ){
			fclose($this->fh);
		}

		if( !empty($this->ch) ){
			curl_close($this->ch);
		}
	}
}

