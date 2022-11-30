<?php

namespace App\services;

//use App\services\Contracts\HttpClient;


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
     * App\services\Contracts\HttpClient;
     */
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function normalizer(string $input): string
    {
        $input = strtolower($input);
        $input = preg_replace('/[-,]/', ' ', $input);
        return strtr(utf8_decode($input), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }

    public function getCity(string $postalCode, string $city): array
    {
        $response = $this->client->request(
            'GET',
            'https://apicarto.ign.fr/api/codes-postaux/communes/' . $postalCode
        );
        $contentData = $response->toArray();
        if (!is_array($contentData)) {
            throw new \RuntimeException('bad format data');
        }
        $cityFound = null;
        $normalizedCity = $this->normalizer($city);
        foreach ($contentData as $cityData) {
            if ($this->normalizer($cityData['nomCommune']) === $normalizedCity) {
                $cityFound = $cityData;
                break;
            }
        }
        if ($cityFound === null) {
            $cityFound = $contentData[0] ?? null;
        }
        if ($cityFound === null) {
            throw new \RuntimeException('no data found');
        }
        $codeInsee = $cityFound['codeCommune'];
        $urlApiCommune = 'https://geo.api.gouv.fr/communes/' . $codeInsee
            . '?fields=nom,code,codesPostaux,siren,centre,codeEpci,codeDepartement,codeRegion,population,departement&format=json&geometry=centre';
        $response2 = $this->client->request(
            'GET',
            $urlApiCommune
        );
        $response2Data = $response2->toArray();
        $statusCode = $response2->getStatusCode();
        if ($statusCode === 400) {
            throw new \RuntimeException('Requête mal formée');
        }

        if ($statusCode === 404) {
            throw new \RuntimeException('Ressource non trouvée');
        }
        if ($statusCode === 200) {
            return [
                'city' => $response2Data['nom'],
                'cp' => $response2Data['codesPostaux'][0],
                'lat' => $response2Data['centre']['coordinates'][1],
                'lon' => $response2Data['centre']['coordinates'][0],
                'departement' => $response2Data['departement']['nom']];
        }
        return [
            'city' => 'unknown',
            'cp' => 'unknown',
            'lat' => '0',
            'lon' => '0',
            'departement' => 'unknown'];

    }
}
//$statusCode = $response->getStatusCode();
// $statusCode = 200
//$contentType = $response->getHeaders()['content-type'][0];
// $contentType = 'application/json'
//$content = $response->getContent();
// $content = '{"id":521583, "name":"symfony-docs", ...}'
// $content = ['id' => 521583, 'name' => 'symfony-docs', ...]