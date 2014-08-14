Flex \ RestClient
============

Gère les méthodes http GET, POST (creation), PUT (mise à jour) et DELETE


Installation
------------

Ajouter à votre composer.json, le dépot suivant

	"repositories": [
        {
            "type": "composer",
            "url": "http://packagist.flex-multimedia.dev/"
        }
    ]

 et ajouter au require

	"flex/rest-client": "dev-master"


Exemple d'utilisation
---------------------

Récupérer une ressource

	use Flex\RestClient;

	$Client = new RestClient\Client('http://monapi/ressources/1');
	$Response = $Client->execute();

	if($Response->isOk()){
		$ressource = $Response->getJsonDecode();
	}

Ajouter une ressource

	use Flex\RestClient;

	$body = array( 'name' => 'ma ressource' );

	$Client = new RestClient\Client('http://monapi/ressources', RestClient\Method::POST, $body );
	$Response = $Client->execute();

	if($Response->isCreated()){
		$id = $Response->getBody();
	}

Mettre à jour une ressource

	use Flex\RestClient;

	$body = array( 'name' => 'nouveau nom' );

	$Client = new RestClient\Client('http://monapi/ressources/1', RestClient\Method::PUT, $body );
	$Response = $Client->execute();

	if($Response->isSuccessful()){
		$id = $Response->getBody();
	}

Supprimer une ressource

	use Flex\RestClient;

	$Client = new RestClient\Client('http://monapi/ressources/1', RestClient\Method::DELETE);
	$Response = $Client->execute();

	if($Response->isSuccessful()){
		// delete ok
	}

Requètes en parallèle

	use Flex\RestClient;

	$ClientCollection = new RestClient\ClientCollection();
	$ClientCollection->set('res1', RestClient\Client('http://monapi/ressources/1');
	$ClientCollection->set('res2', RestClient\Client('http://monapi/ressources/2');
	$ClientCollection->set('res42', RestClient\Client('http://monapi/ressources/42');

	$ResponseCollection = $ClientCollection->execute();

	$body42 = $ResponseCollection['res42']->getBody();

	foreach($ResponseCollection as $Response){
		if($Response->isSuccessful()){
			// do something
		}
	}