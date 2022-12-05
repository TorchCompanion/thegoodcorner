<?php

namespace App\services;

class AdService
{
    public function getAds(): array
    {
        $ads = [
            [
                'title' => 'Titre pub 1',
                'texte' => 'Ceci est le texte de la pub 1',
                'image' => '/assets/images/dunno.jpg',
            ],
            [
                'title' => 'Titre pub 2',
                'texte' => 'Ceci est le texte de la pub 2',
                'image' => '/assets/images/hedgehog.jpg',
            ],
            [
                'title' => 'Titre pub 3',
                'texte' => 'Ceci est le texte de la pub 3',
                'image' => '/assets/images/seal.jpg',
            ],
            [
                'title' => 'Titre pub 4',
                'texte' => 'Ceci est le texte de la pub 4',
                'image' => '/assets/images/snuk.jpg',
            ],
        ];
        $advertise = [];
        while (count($advertise) < 2) {
            $advertise[] = $ads[random_int(0, count($ads) - 1)];
        }
        return $advertise;
    }
}