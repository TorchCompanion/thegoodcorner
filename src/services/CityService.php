<?php

namespace App\services;

// API postal to INSEE : https://api.gouv.fr/documentation/api_carto_codes_postaux
// API INSEE to data : https://api.gouv.fr/documentation/api-geo
// EP =================== /commune/{code}

// HTPP CLIENT
// https://symfony.com/doc/current/http_client.html

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CityService
{

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getCity(string $postalCode, string $city): array
    {
        $resp = $this->client->request('GET', 'https://api.gouv.fr/documentation/api_carto_codes_postaux?code=' . $postalCode);

        if($resp['code'] === $postalCode && $resp['nom'] === $city) {
            $response = $this->client->request('GET', 'https://api.gouv.fr/documentation/api-geo');
            return $response->toArray();
        }
        return $resp[0];
        // $response = $this->client->request('GET', 'https://geo.api.gouv.fr/communes?nom=' . $city . '&fields=nom,code,codesPostaux,centre,codeDepartement,codeRegion,population&format=json&geometry=centre');
        // on check si le nom de la ville correspond a un des element de la reponse
        // de la premiére API, si non on prend le premier element du tableau
        // $resp[0]
    }
}