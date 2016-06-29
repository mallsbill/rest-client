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

	"flex/rest-client": "0.*"


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
	// ajoute la suite de la collection
	$ClientCollection->add(new RestClient\Client('http://monapi/ressources/1');
	// ajoute en définissant la clé
	$ClientCollection->set('res2', new RestClient\Client('http://monapi/ressources/2');
	// ajoute en définissant la clé, utilise offsetSet d'ArrayAccess
	$ClientCollection['res42'] = new RestClient\Client('http://monapi/ressources/42');

	// on exécute les requètes
	$ResponseCollection = $ClientCollection->execute();

	// on récupère le retour de la requète ajouté avec add (index 0)
	$body = $ResponseCollection->get(0)->getBody();
	// on récupère le retour de la requète res2 grâce à offsetGet d'ArrayAccess
	$body2 = $ResponseCollection['res2']->getBody();
	// on récupère le retour de la requète res42
	$body = $ResponseCollection->get('res42')->getBody();

	// on peut aussi boucler sur la collection
	foreach($ResponseCollection as $Response){
		if($Response->isSuccessful()){
			// do something
		}
	}