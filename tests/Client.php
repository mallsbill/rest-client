<?php
namespace Flex\RestClient\tests\units;

use Flex\RestClient\Client as TestedClass;
use Flex\RestClient\Method;
use Flex\RestClient\MineType;
use	mageekguy\atoum;

class Client extends atoum\test
{
    public function testPost()
    {

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

        $client = new TestedClass('http://jsonplaceholder.typicode.com/posts', Method::POST, $body);
        $response = $client->execute();

        $this->object($response)->isInstanceOf('\\Flex\RestClient\\Response');
        $this->boolean($response->isSuccessful())->isTrue();
    }

    public function testGet()
    {
        $body = array(
                'title' => 'sunt aut facere repellat provident occaecati excepturi optio reprehenderit',
                'body' =>
'quia et suscipit
suscipit recusandae consequuntur expedita et cum
reprehenderit molestiae ut ut quas totam
nostrum rerum est autem sunt rem eveniet architecto',
                'userId' => 1
            );

        $client = new TestedClass('http://jsonplaceholder.typicode.com/posts/1');
        $response = $client->execute();

        $this->boolean($response->isSuccessful())->isTrue();

        $res2 = $response->getJsonDecode();

        $this->integer($res2->id)->isEqualTo(1);
        $this->string($res2->title)->isEqualTo($body['title']);
        $this->string($res2->body)->isEqualTo($body['body']);
        $this->integer($res2->userId)->isEqualTo($body['userId']);
        
        $headers = $response->getHeaders();
        $this->string($headers[0])->isEqualTo('HTTP/1.1 200 OK');
    }

    public function testUpdate()
    {
        // update
        $body_update = array(
                'title' => 'nesciunt quas odio',
                'body' =>
'repudiandae veniam quaerat sunt sed
alias aut fugiat sit autem sed est
voluptatem omnis possimus esse voluptatibus quis
est aut tenetur dolor neque',
                'userId' => 2
            );

        $client = new TestedClass('http://jsonplaceholder.typicode.com/posts/99', Method::PUT, $body_update);
        $response = $client->execute();
        $this->boolean($response->isSuccessful())->isTrue();

        $body = $response->getJsonDecode(true);
        
        $this->array($body)->hasKey('userId');
        $this->string($body['userId'])->isEqualTo('2');
    }

    public function testDelete()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/posts/100', Method::DELETE);
        $response = $client->execute();
        $this->boolean($response->isSuccessful())->isTrue();
    }

    public function testExceptions()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/posts/1', 'UNKNOW');
        $this->exception(
            function () use ($client) {
                $client->execute();
            }
        )->hasMessage('Current verb (UNKNOW) is an invalid REST method.');

        $client = new TestedClass();
        $this->exception(
            function () use ($client) {
                $client->execute();
            }
        )->hasMessage('Url must be set');
    }

    public function testGetCurlHandler()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');

        $this->exception(
            function () use ($client) {
                $client->getCurlHandler();
            }
        )->hasMessage('Curl handler not initialized');

        $client->init();

        $this->variable($client->getCurlHandler())->isNotNull();
    }

    public function testSetContentType()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
        $client->setContentType(MineType::TEXT);

        $this->string($client->getContentType())->isEqualTo(MineType::TEXT);
    }

    public function testSetAcceptType()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
        $this->string($client->getAcceptType())->isEqualTo(MineType::JSON);

        $client->setAcceptType(MineType::TEXT);

        $this->string($client->getAcceptType())->isEqualTo(MineType::TEXT);
    }

    public function testSetPassword()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
        $client->setUsername('username');
        $client->setPassword('password');
        $client->init();

        $this->string($client->getPassword())->isEqualTo('password');
        $this->string($client->getUsername())->isEqualTo('username');
    }

    public function testSetUrl()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
        $this->string($client->getUrl())->isEqualTo('http://jsonplaceholder.typicode.com/users/1');
        
        $client->setUrl('http://www.google.fr');

        $this->string($client->getUrl())->isEqualTo('http://www.google.fr');
    }

    public function testSetMethod()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
        $this->string($client->getMethod())->isEqualTo(Method::GET);

        $client->setMethod(Method::POST);

        $this->string($client->getMethod())->isEqualTo(Method::POST);
    }

    public function testSetTimeout()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
        $this->integer($client->getTimeout())->isEqualTo(5);

        $client->setTimeout(10);

        $this->integer($client->getTimeout())->isEqualTo(10);

        $this->exception(
            function () use ($client) {
                $client->setTimeout('pouet');
            }
        )->isInstanceOf('\InvalidArgumentException');
    }

    public function testSetSslVerify()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
        $this->boolean($client->getSslVerify())->isFalse();

        $client->setSslVerify(true);

        $this->boolean($client->getSslVerify())->isTrue();

        $this->exception(
            function () use ($client) {
                $client->setSslVerify('true');
            }
        )->isInstanceOf('\InvalidArgumentException');
    }

    public function testSetFollowLocation()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
        $this->boolean($client->getFollowLocation())->isTrue();

        $client->setFollowLocation(false);

        $this->boolean($client->getFollowLocation())->isFalse();

        $this->exception(
            function () use ($client) {
                $client->setFollowLocation('true');
            }
        )->isInstanceOf('\InvalidArgumentException');
    }

    public function testSetCookiePersistence()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
        $this->boolean($client->getCookiePersistence())->isFalse();

        $client->setCookiePersistence(true);

        $this->boolean($client->getCookiePersistence())->isTrue();

        $this->exception(
            function () use ($client) {
                $client->setCookiePersistence('true');
            }
        )->isInstanceOf('\InvalidArgumentException');
    }

    public function testSetUserAgent()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
        $this->variable($client->getUserAgent())->isNull();

        $client->setUserAgent('Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36');

        $this->string($client->getUserAgent())->isEqualTo('Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36');
    }

    public function testHeaders()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');

        $this->string($client->getHeader('Accept'))->isEqualTo(MineType::JSON);
        
        $client->setHeader('Accept-Language', 'fr-FR');
        $this->string($client->getHeader('Accept-Language'))->isEqualTo('fr-FR');

        $client->setContentType(MineType::TEXT);
        $this->string($client->getHeader('Content-Type'))->isEqualTo(MineType::TEXT);

        $this->array($client->getHeaders())->hasKeys(array('Accept','Accept-Language','Content-Type'));
    }

    public function testSslVersion()
    {
        $client = new TestedClass('https://www.payflex.ch');
        $this->integer($client->getSslVersion())->isEqualTo(0);
        $response = $client->execute();
        $this->integer($response->getHttpCode())->isEqualTo(200);

        $client = new TestedClass('https://www.payflex.ch');
        $client->setSslVersion(6);
        $this->integer($client->getSslVersion())->isEqualTo(6);
        $response = $client->execute();
        $this->integer($response->getHttpCode())->isEqualTo(200);
    }

    public function testSetCookieJarDirectory()
    {
        $client = new TestedClass('http://jsonplaceholder.typicode.com/users/1');
        $this->string($client->getCookieJarDirectory())->isEqualTo('/tmp');

        $client->setCookieJarDirectory(__DIR__);

        $this->string($client->getCookieJarDirectory())->isEqualTo(__DIR__);

        $this->exception(
            function () use ($client) {
                $client->setCookieJarDirectory('/bad_directory');
            }
        )->isInstanceOf('\LogicException')->hasMessage('/bad_directory is not a valid directory');
    }
}
