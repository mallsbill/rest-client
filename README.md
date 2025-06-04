Pephpit \ RestClient
============

Gère les méthodes http GET, POST (creation), PUT (mise à jour) et DELETE


Installation
------------

Ajouter à votre composer.json, le dépot suivant

	"repositories": [
        {
            "type": "git",
            "url": "https://github.com/mallsbill/rest-client.git"
        }
    ]

 et ajouter au require

	composer require pephpit/rest-client


Exemple d'utilisation
---------------------

Récupérer une ressource

	use Pephpit\RestClient\Client;

	$client = new Client('http://monapi/ressources/1');
	$response = $client->execute();

	if($response->isOk()){
		$ressource = $response->getJsonDecode();
	}

Ajouter une ressource

	use Pephpit\RestClient\Client;
    use Pephpit\RestClient\Method;

	$body = array( 'name' => 'ma ressource' );

	$client = new Client('http://monapi/ressources', Method::POST, $body );
	$response = $client->execute();

	if($response->isCreated()){
		$id = $response->getBody();
	}

Mettre à jour une ressource

	use Pephpit\RestClient\Client;
    use Pephpit\RestClient\Method;

	$body = array( 'name' => 'nouveau nom' );

	$client = new Client('http://monapi/ressources/1', Method::PUT, $body );
	$response = $client->execute();

	if($response->isSuccessful()){
		$id = $response->getBody();
	}

Supprimer une ressource

	use Pephpit\RestClient\Client;
    use Pephpit\RestClient\Method;

	$client = new Client('http://monapi/ressources/1', Method::DELETE);
	$response = $client->execute();

	if($response->isSuccessful()){
		// delete ok
	}

Requètes en parallèle

    use Pephpit\RestClient\Client;	
    use Pephpit\RestClient\ClientCollection;

	$clientCollection = new RestClient\ClientCollection();
	// ajoute la suite de la collection
	$clientCollection->add(new RestClient\Client('http://monapi/ressources/1');
	// ajoute en définissant la clé
	$clientCollection->set('res2', new RestClient\Client('http://monapi/ressources/2');
	// ajoute en définissant la clé, utilise offsetSet d'ArrayAccess
	$clientCollection['res42'] = new RestClient\Client('http://monapi/ressources/42');

	// on exécute les requètes
	$responseCollection = $clientCollection->execute();

	// on récupère le retour de la requète ajouté avec add (index 0)
	$body = $responseCollection->get(0)->getBody();
	// on récupère le retour de la requète res2 grâce à offsetGet d'ArrayAccess
	$body2 = $responseCollection['res2']->getBody();
	// on récupère le retour de la requète res42
	$body = $responseCollection->get('res42')->getBody();

	// on peut aussi boucler sur la collection
	foreach($responseCollection as $response){
		if($response->isSuccessful()){
			// do something
		}
	}

Tests unitaires
---------------

Lancer tout les tests

	composer test

Lancer un test en particulier

	composer atoum -- -f tests/Client.php

