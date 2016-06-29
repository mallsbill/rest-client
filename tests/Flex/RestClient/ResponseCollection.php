<?php
namespace Flex\RestClient\tests\units;

require_once realpath(dirname(__FILE__)).'/../../config/config.php';

use Flex\RestClient\ResponseCollection as TestedClass;
use mageekguy\atoum;

Class ResponseCollection extends atoum\test {

	public function testExceptions() {
		$ResponseCollection = new TestedClass();

		$this->exception(
			function() use($ResponseCollection) {
				$ResponseCollection->add(new \stdClass());
			}
		)->isInstanceOf('\\InvalidArgumentException')->hasMessage('$response must be an instance of \Flex\RestClient\Response');

		$this->exception(
			function() use($ResponseCollection) {
				$ResponseCollection->set('stdclass', new \stdClass());
			}
		)->isInstanceOf('\\InvalidArgumentException')->hasMessage('$response must be an instance of \Flex\RestClient\Response');

		$this->exception(
			function() use($ResponseCollection) {
			$ResponseCollection['stdclass'] = new \stdClass();
			}
		)->isInstanceOf('\\InvalidArgumentException')->hasMessage('$response must be an instance of \Flex\RestClient\Response');

	}


}
