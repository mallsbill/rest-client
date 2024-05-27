<?php

namespace Pephpit\RestClient\tests\units;

use Pephpit\RestClient\ResponseCollection as TestedClass;
use atoum;

class ResponseCollection extends atoum
{

    public function testSetGet()
    {
        $response = new \Pephpit\RestClient\Response('', array(), '');

        $ResponseCollection = new TestedClass();
        $ResponseCollection->add($response);
        $ResponseCollection->set('response1', $response);
        $ResponseCollection['response2'] = $response;

        $this->object($ResponseCollection[0])->isInstanceOf('\\Pephpit\RestClient\\Response');
        $this->object($ResponseCollection['response1'])->isInstanceOf('\\Pephpit\RestClient\\Response');
        $this->object($ResponseCollection->get('response2'))->isInstanceOf('\\Pephpit\RestClient\\Response');
        $this->variable($ResponseCollection['unknow'])->isNull();
    }

    public function testExceptions()
    {
        $ResponseCollection = new TestedClass();

        $this->exception(
                function () use ($ResponseCollection) {
                    $ResponseCollection->add(new \stdClass());
                }
        )->isInstanceOf('\\InvalidArgumentException')->hasMessage('$response must be an instance of \Pephpit\RestClient\Response');

        $this->exception(
                function () use ($ResponseCollection) {
                    $ResponseCollection->set('stdclass', new \stdClass());
                }
        )->isInstanceOf('\\InvalidArgumentException')->hasMessage('$response must be an instance of \Pephpit\RestClient\Response');

        $this->exception(
                function () use ($ResponseCollection) {
                    $ResponseCollection['stdclass'] = new \stdClass();
                }
        )->isInstanceOf('\\InvalidArgumentException')->hasMessage('$response must be an instance of \Pephpit\RestClient\Response');
    }
}
