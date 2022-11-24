<?php

namespace Flex\RestClient\tests\units;

use Flex\RestClient\Response as TestedClass;
use Flex\RestClient\Client;
use atoum;

class Response extends atoum
{

    public function testGetBody()
    {
        $body = '{
  "id": 1,
  "name": "Leanne Graham",
  "username": "Bret",
  "email": "Sincere@april.biz",
  "address": {
    "street": "Kulas Light",
    "suite": "Apt. 556",
    "city": "Gwenborough",
    "zipcode": "92998-3874",
    "geo": {
      "lat": "-37.3159",
      "lng": "81.1496"
    }
  },
  "phone": "1-770-736-8031 x56442",
  "website": "hildegard.org",
  "company": {
    "name": "Romaguera-Crona",
    "catchPhrase": "Multi-layered client-server neural-net",
    "bs": "harness real-time e-markets"
  }
}';

        $response = new TestedClass($body, array(), array());

        $this->string($response->getBody())->isNotNull();
        $this->object($response->getJsonDecode())->isInstanceOf('\\stdClass');
        $this->array($response->getJsonDecode(true))->hasKeys(array('id', 'name'));
    }

    public function testGetInfos()
    {
        $infos = array(
            'url' => 'http://jsonplaceholder.typicode.com/users/1',
            'content_type' => 'application/json; charset=utf-8',
            'http_code' => 200
        );

        $response = new TestedClass('', $infos, array());
        $this->array($response->getInfos())->hasKeys(array('url', 'content_type', 'http_code'));
    }

    public function testGetHeaders()
    {
        $headers = array(
            0 => "HTTP/1.1 200 OK",
            'Content-Type' => " application/json; charset=utf-8",
            'Content-Length' => " 509"
        );

        $response = new TestedClass('', array(), array(), $headers);
        $this->array($response->getHeaders())->isNotEmpty();
        $this->array($response->getHeaders())->hasKeys(array(0, 'Content-Type', 'Content-Length'));
    }

    public function testGetContentType()
    {
        $response = new TestedClass('', array('content_type' => 'application/json; charset=utf-8'), '');
        $this->string($response->getContentType())->isEqualTo('application/json; charset=utf-8');
    }

    public function testCheckContentType()
    {
        $response = new TestedClass('', array('content_type' => 'application/json; charset=utf-8'), '');
        $this->boolean($response->checkContentType('application/json; charset=utf-8'))->isTrue();
        $this->boolean($response->checkContentType('text/plain'))->isFalse();
    }

    public function testIsInformation()
    {
        $response = new TestedClass('', array('http_code' => 101), '');
        $this->boolean($response->isInformation())->isTrue();
    }

    public function testIsSuccessful()
    {
        $response = new TestedClass('', array('http_code' => 201), '');
        $this->boolean($response->isSuccessful())->isTrue();

        $response = new TestedClass('', array('http_code' => 300), '');
        $this->boolean($response->isSuccessful())->isFalse();
    }

    public function testIsRedirection()
    {
        $response = new TestedClass('', array('http_code' => 301), '');
        $this->boolean($response->isRedirection())->isTrue();
    }

    public function testIsClientError()
    {
        $response = new TestedClass('', array('http_code' => 400), '');
        $this->boolean($response->isClientError())->isTrue();
    }

    public function testIsServerError()
    {
        $response = new TestedClass('', array('http_code' => 500), '');
        $this->boolean($response->isServerError())->isTrue();
    }

    public function testIsOk()
    {
        $response = new TestedClass('', array('http_code' => 400), '');
        $this->boolean($response->isOk())->isFalse();
    }

    public function testIsCreated()
    {
        $response = new TestedClass('', array('http_code' => 201), '');
        $this->boolean($response->isCreated())->isTrue();
    }

    public function testIsAccepted()
    {
        $response = new TestedClass('', array('http_code' => 202), '');
        $this->boolean($response->isAccepted())->isTrue();
    }

    public function testIsNotFound()
    {
        $response = new TestedClass('', array('http_code' => 404), '');
        $this->boolean($response->isNotFound())->isTrue();
    }

    public function testGetSimpleXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<note>
<to>Tove</to>
<from>Jani</from>
<heading>Reminder</heading>
<body>Don\'t forget me this weekend!</body>
</note>';
        $response = new TestedClass($xml, array(), array());
        $simpleXmlElement = $response->getSimpleXml();
        $this->object($simpleXmlElement)->isInstanceOf('\\SimpleXMLElement');
        $this->object($simpleXmlElement->to)->isEqualTo('Tove');
    }
}
