<?php
namespace Flex\RestClient\tests\units;

require_once realpath(dirname(__FILE__)).'/../../config/config.php';

use Flex\RestClient\Client as TestedClass;
use Flex\RestClient\Method;
use Flex\RestClient\MineType;
use	mageekguy\atoum;

Class Client extends atoum\test {

	public function testClient(){

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

		$res = $Response->getJsonDecode();

		// get post just created
		$Client = new TestedClass('http://jsonplaceholder.typicode.com/posts/'.$res->id);
		$Response = $Client->execute();
		$this->boolean($Response->isSuccessful())->isTrue();

		$res2 = $Response->getJsonDecode();

		$this->integer($res2->id)->isEqualTo($res->id);
		$this->string($res2->title)->isEqualTo($body['title']);
		$this->string($res2->body)->isEqualTo($body['body']);
		$this->string($res2->userId)->isEqualTo($body['userId']);


		// update
		/*$body_update = array(
				'title' => 'nesciunt quas odio',
				'body' =>
'repudiandae veniam quaerat sunt sed
alias aut fugiat sit autem sed est
voluptatem omnis possimus esse voluptatibus quis
est aut tenetur dolor neque',
				'userId' => 1
			);

		$Client = new TestedClass('http://jsonplaceholder.typicode.com/posts/1'.$res->id, Method::PUT, $body_update);
		$Response = $Client->execute();
		$this->boolean($Response->isSuccessful())->isTrue();

		$res3 = $Response->getJsonDecode();

		$this->integer($res3->id)->isEqualTo($res->id);
		$this->string($res3->title)->isEqualTo($body_update['title']);
		$this->string($res3->body)->isEqualTo($body_update['body']);
		$this->string($res3->userId)->isEqualTo($body_update['userId']);*/

		$Client = new TestedClass('http://jsonplaceholder.typicode.com/posts/'.$res->id, Method::DELETE);
		$Response = $Client->execute();
		$this->boolean($Response->isSuccessful())->isTrue();

	}

	public function testExceptions(){

		$Client = new TestedClass('http://jsonplaceholder.typicode.com/posts/'.$res->id, 'UNKNOW');
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


}
