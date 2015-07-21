<?php
namespace Flex\RestClient\tests\units;

require_once realpath(dirname(__FILE__)).'/../../config/config.php';

use Flex\RestClient\Client as TestedClass;
use Flex\RestClient\Method;
use Flex\RestClient\MineType;
use	mageekguy\atoum;

Class Client extends atoum\test {

	public function testPost() {

		// add new post
		$body = array(
				'title' => 'sunt aut facere repellat provident occaecati excepturi optio reprehenderit',
				'body' =>
'quia et suscipit
suscipit recusandae consequuntur expedita et cum
reprehenderit molestiae ut ut quas totam
nostrum rerum est autem sunt rem eveniet architecto',
				'userId' => 1
			);

		$Client = new TestedClass('http://jsonplaceholder.typicode.com/posts', Method::POST, $body );
		$Response = $Client->execute();

		$this->object($Response)->isInstanceOf('\\Flex\RestClient\\Response');
		$this->boolean($Response->isSuccessful())->isTrue();

	}

	public function testGet() {

		$body = array(
				'title' => 'sunt aut facere repellat provident occaecati excepturi optio reprehenderit',
				'body' =>
'quia et suscipit
suscipit recusandae consequuntur expedita et cum
reprehenderit molestiae ut ut quas totam
nostrum rerum est autem sunt rem eveniet architecto',
				'userId' => 1
			);

		$Client = new TestedClass('http://jsonplaceholder.typicode.com/posts/1');
		$Response = $Client->execute();

		$this->boolean($Response->isSuccessful())->isTrue();

		$res2 = $Response->getJsonDecode();

		$this->integer($res2->id)->isEqualTo(1);
		$this->string($res2->title)->isEqualTo($body['title']);
		$this->string($res2->body)->isEqualTo($body['body']);
		$this->integer($res2->userId)->isEqualTo($body['userId']);

	}

	public function testUpdate() {
		// update
		$body_update = array(
				'title' => 'nesciunt quas odio',
				'body' =>
'repudiandae veniam quaerat sunt sed
alias aut fugiat sit autem sed est
voluptatem omnis possimus esse voluptatibus quis
est aut tenetur dolor neque',
				'userId' => 1
			);

		$Client = new TestedClass('http://jsonplaceholder.typicode.com/posts/99', Method::PUT, $body_update);
		$Response = $Client->execute();
		$this->boolean($Response->isSuccessful())->isTrue();
	}

	public function testDelete() {
		$Client = new TestedClass('http://jsonplaceholder.typicode.com/posts/100', Method::DELETE);
		$Response = $Client->execute();
		$this->boolean($Response->isSuccessful())->isTrue();

	}

	public function testExceptions(){

		$Client = new TestedClass('http://jsonplaceholder.typicode.com/posts/1', 'UNKNOW');
		$this->exception(
			function() use($Client) {
				$Client->execute();
			}
		)->hasMessage('Current verb (UNKNOW) is an invalid REST method.');

		$Client = new TestedClass();
		$this->exception(
			function() use($Client) {
				$Client->execute();
			}
		)->hasMessage('Url must be set');

	}

	public function testGetCurlHandler(){
		$Client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');

		$this->exception(
			function() use($Client) {
				$Client->getCurlHandler();
			}
		)->hasMessage('Curl handler not initialized');

		$Client->init();

		$this->variable($Client->getCurlHandler())->isNotNull();
	}

	public function testSetContentType() {
		$Client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
		$Client->setContentType(MineType::TEXT);

		$this->string($Client->getContentType())->isEqualTo(MineType::TEXT);
	}

	public function testSetAcceptType() {
		$Client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
		$this->string($Client->getAcceptType())->isEqualTo(MineType::JSON);

		$Client->setAcceptType(MineType::TEXT);

		$this->string($Client->getAcceptType())->isEqualTo(MineType::TEXT);
	}

	public function testSetPassword() {
		$Client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
		$Client->setUsername('username');
		$Client->setPassword('password');
		$Client->init();

		$this->string($Client->getPassword())->isEqualTo('password');
		$this->string($Client->getUsername())->isEqualTo('username');
	}

	public function testSetUrl() {
		$Client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
		$this->string($Client->getUrl())->isEqualTo('http://jsonplaceholder.typicode.com/users/1');
		
		$Client->setUrl('http://www.google.fr');

		$this->string($Client->getUrl())->isEqualTo('http://www.google.fr');
	}

	public function testSetMethod() {
		$Client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
		$this->string($Client->getMethod())->isEqualTo(Method::GET);

		$Client->setMethod(Method::POST);

		$this->string($Client->getMethod())->isEqualTo(Method::POST);
	}

	public function testSetTimeout() {
		$Client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
		$this->integer($Client->getTimeout())->isEqualTo(5);

		$Client->setTimeout(10);

		$this->integer($Client->getTimeout())->isEqualTo(10);

		$this->exception(
			function() use($Client) {
				$Client->setTimeout('pouet');
			}
		)->isInstanceOf('\InvalidArgumentException');
	}

	public function testSetSslVerify() {
		$Client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
		$this->boolean($Client->getSslVerify())->isFalse();

		$Client->setSslVerify(true);

		$this->boolean($Client->getSslVerify())->isTrue();

		$this->exception(
			function() use($Client) {
				$Client->setSslVerify('true');
			}
		)->isInstanceOf('\InvalidArgumentException');
	}

	public function testSetFollowLocation() {
		$Client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
		$this->boolean($Client->getFollowLocation())->isTrue();

		$Client->setFollowLocation(false);

		$this->boolean($Client->getFollowLocation())->isFalse();

		$this->exception(
			function() use($Client) {
				$Client->setFollowLocation('true');
			}
		)->isInstanceOf('\InvalidArgumentException');
	}

	public function testHeaders() {
		$Client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');

		$this->string($Client->getHeader('Accept'))->isEqualTo(MineType::JSON);
		
		$Client->setHeader('Accept-Language', 'fr-FR');
		$this->string($Client->getHeader('Accept-Language'))->isEqualTo('fr-FR');

		$Client->setContentType(MineType::TEXT);
		$this->string($Client->getHeader('Content-Type'))->isEqualTo(MineType::TEXT);

		$this->array($Client->getHeaders())->hasKeys(array('Accept','Accept-Language','Content-Type'));
		
	}

}
