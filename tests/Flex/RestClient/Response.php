<?php
namespace Flex\RestClient\tests\units;

require_once realpath(dirname(__FILE__)).'/../../config/config.php';

use Flex\RestClient\Response as TestedClass;
use Flex\RestClient\Client;
use mageekguy\atoum;

Class Response extends atoum\test {

	public function testGet(){

		$Client = new Client('http://jsonplaceholder.typicode.com/users/1');
		$Response = $Client->execute();

		$this->object($Response)->isInstanceOf('\\Flex\RestClient\\Response');

		$this->string($Response->getBody())->isNotNull();
		$this->object($Response->getJsonDecode())->isInstanceOf('\\stdClass');
		$this->array($Response->getJsonDecode(true))->hasKeys(array('id','name'));

		$this->array($Response->getInfos())->hasKeys(array('url','content_type','http_code'));
		$this->integer($Response->getHttpCode())->isEqualTo(200);
		$this->boolean($Response->isOk())->isTrue();
		$this->boolean($Response->isSuccessful())->isTrue();
		$this->boolean($Response->isInformation())->isFalse();
		$this->boolean($Response->isRedirection())->isFalse();
		$this->boolean($Response->isClientError())->isFalse();
		$this->boolean($Response->isServerError())->isFalse();
		$this->boolean($Response->isCreated())->isFalse();
		$this->boolean($Response->isAccepted())->isFalse();
		$this->boolean($Response->isNotFound())->isFalse();

		$this->string($Response->getContentType())->isEqualTo('application/json; charset=utf-8');
		$this->boolean($Response->checkContentType('application/json; charset=utf-8'))->isTrue();
		$this->boolean($Response->checkContentType('text/plain'))->isFalse();

		$this->array($Response->getHeaders())->isNotEmpty();
		$headers = $Response->getHeaders();
		$this->string($headers[0])->isEqualTo('HTTP/1.1 200 OK');

	}

	public function testIsInformation() {

		$Response = new TestedClass('',array('http_code'=>101),'');
		$this->boolean($Response->isInformation())->isTrue();

	}

	public function testIsSuccessful() {
		$Response = new TestedClass('',array('http_code'=>201),'');
		$this->boolean($Response->isSuccessful())->isTrue();

		$Response = new TestedClass('',array('http_code'=>300),'');
		$this->boolean($Response->isSuccessful())->isFalse();
	}

	public function testIsRedirection() {
		$Response = new TestedClass('',array('http_code'=>301),'');
		$this->boolean($Response->isRedirection())->isTrue();
	}

	public function testIsClientError() {
		$Response = new TestedClass('',array('http_code'=>400),'');
		$this->boolean($Response->isClientError())->isTrue();
	}

	public function testIsServerError() {
		$Response = new TestedClass('',array('http_code'=>500),'');
		$this->boolean($Response->isServerError())->isTrue();
	}

	public function testIsOk() {
		$Response = new TestedClass('',array('http_code'=>400),'');
		$this->boolean($Response->isOk())->isFalse();
	}

	public function testIsCreated() {
		$Response = new TestedClass('',array('http_code'=>201),'');
		$this->boolean($Response->isCreated())->isTrue();
	}

	public function testIsAccepted() {
		$Response = new TestedClass('',array('http_code'=>202),'');
		$this->boolean($Response->isAccepted())->isTrue();
	}

	public function testIsNotFound() {
		$Response = new TestedClass('',array('http_code'=>404),'');
		$this->boolean($Response->isNotFound())->isTrue();
	}


}
