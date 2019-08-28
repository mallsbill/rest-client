<?php
namespace Flex\RestClient\tests\units;

use Flex\RestClient\Client;
use Flex\RestClient\ClientCollection as TestedClass;
use mageekguy\atoum;

class ClientCollection extends atoum\test {

	public function getCollection(){
		$ClientCollection = new TestedClass();

		$ClientCollection->add(new Client('http://jsonplaceholder.typicode.com/posts'));
		$ClientCollection->set('comments', new Client('http://jsonplaceholder.typicode.com/comments'));
		$ClientCollection['users'] = new Client('http://jsonplaceholder.typicode.com/users');

		return $ClientCollection;
	}

	public function testGet(){

		$ClientCollection = $this->getCollection();

		$this->object($ClientCollection[0])->isInstanceOf('\\Flex\RestClient\\Client');
		$this->object($ClientCollection['comments'])->isInstanceOf('\\Flex\RestClient\\Client');
		$this->object($ClientCollection->get('users'))->isInstanceOf('\\Flex\RestClient\\Client');
		$this->variable($ClientCollection['unknow'])->isNull();

	}

	public function testCount(){
		$ClientCollection = $this->getCollection();

		$this->integer(count($ClientCollection))->isEqualTo(3);
	}

	public function testNav(){
		$ClientCollection = $this->getCollection();

		$this->object($ClientCollection->first())->isInstanceOf($ClientCollection[0]);
		$this->object($ClientCollection->next())->isIdenticalTo($ClientCollection['comments']);
		$this->object($ClientCollection->current())->isIdenticalTo($ClientCollection['comments']);
		$this->object($ClientCollection->last())->isIdenticalTo($ClientCollection['users']);
		$this->string($ClientCollection->key())->isEqualTo('users');
	}

	public function testExist(){
		$ClientCollection = $this->getCollection();
		
		$this->boolean($ClientCollection->exists(0))->isTrue();
		$ClientCollection->remove(0);
		$this->boolean($ClientCollection->exists(0))->isFalse();

		$this->boolean(isset($ClientCollection['comments']))->isTrue();
		unset($ClientCollection['comments']);
		$this->boolean(isset($ClientCollection['comments']))->isFalse();

	}

	public function testGetIterator(){
		$ClientCollection = $this->getCollection();

		foreach ($ClientCollection as $key => $Client){
			$this->object($Client)->isInstanceOf('\\Flex\RestClient\\Client');
		}

	}

	public function testRequest(){
		$ClientCollection = $this->getCollection();

		$ResponseCollection = $ClientCollection->execute();

		$this->integer(count($ResponseCollection))->isEqualTo(3);
		$this->object($ResponseCollection)->isInstanceOf('\\Flex\RestClient\\ResponseCollection');
		$this->object($ResponseCollection[0])->isInstanceOf('\\Flex\RestClient\\Response');
		$this->object($ResponseCollection['comments'])->isInstanceOf('\\Flex\RestClient\\Response');
		$this->object($ResponseCollection['users'])->isInstanceOf('\\Flex\RestClient\\Response');
		$this->boolean($ResponseCollection[0]->isSuccessful())->isTrue();
		$this->boolean($ResponseCollection['comments']->isSuccessful())->isTrue();
		$this->boolean($ResponseCollection['users']->isSuccessful())->isTrue();

	}

	public function testExceptions() {
		$ClientCollection = new TestedClass();

		$this->exception(
			function() use($ClientCollection) {
				$ClientCollection->add(new \stdClass());
			}
		)->isInstanceOf('\\InvalidArgumentException')->hasMessage('$client must be an instance of \Flex\RestClient\Client');

		$this->exception(
			function() use($ClientCollection) {
				$ClientCollection->set('stdclass', new \stdClass());
			}
		)->isInstanceOf('\\InvalidArgumentException')->hasMessage('$client must be an instance of \Flex\RestClient\Client');

		$this->exception(
			function() use($ClientCollection) {
			$ClientCollection['stdclass'] = new \stdClass();
			}
		)->isInstanceOf('\\InvalidArgumentException')->hasMessage('$client must be an instance of \Flex\RestClient\Client');

	}


}
