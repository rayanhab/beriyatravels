<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Departure;
use App\Models\Offer;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            [
                'name' => 'Saïdia',
                'slug' => 'saidia',
                'description' => 'La côte méditerranéenne et la marina de Saïdia.',
                'image_url' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/6/62/Saidia_MOROCCO_BEACH.jpg/960px-Saidia_MOROCCO_BEACH.jpg',
                'image_alt' => 'Plage de Saïdia, sur la côte méditerranéenne du Maroc',
            ],
            [
                'name' => 'Nador & Marchica',
                'slug' => 'nador-marchica',
                'description' => 'La lagune Marchica et les panoramas du littoral de l’Oriental.',
                'image_url' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/f/fd/Lagune_Marchica.jpg/960px-Lagune_Marchica.jpg',
                'image_alt' => 'Lagune Marchica, près de Nador, dans la région de l’Oriental',
            ],
            [
                'name' => 'Fès',
                'slug' => 'fes',
                'description' => 'La médina historique de Fès et ses quartiers emblématiques.',
                'image_url' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/7/7c/Looking_Upon_The_Medina_in_Fes%2C_Morocco.jpg/960px-Looking_Upon_The_Medina_in_Fes%2C_Morocco.jpg',
                'image_alt' => 'Vue sur la médina historique de Fès',
            ],
            [
                'name' => 'Marrakech',
                'slug' => 'marrakech',
                'description' => 'La ville rouge, ses jardins et la place Jemaa el-Fna.',
                'image_url' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/7/7a/Jamaa_El_Fna%2C_Marrakesh%2C_Morocco%2C_20250124_1804_7017.jpg/960px-Jamaa_El_Fna%2C_Marrakesh%2C_Morocco%2C_20250124_1804_7017.jpg',
                'image_alt' => 'Place Jemaa el-Fna, au cœur de Marrakech',
            ],
        ];

        foreach ($destinations as $destinationData) {
            $destination = Destination::query()->updateOrCreate(
                ['slug' => $destinationData['slug']],
                $destinationData
            );

            $offer = match ($destination->slug) {
                'saidia' => [
                    'title' => 'Saïdia Express',
                    'category' => 'Mer & détente',
                    'summary' => 'Une journée iodée sur la côte, avec du temps libre pour la plage et la marina.',
                    'badge' => 'Une journée',
                    'duration' => '1 jour',
                    'includes' => 'Transport aller-retour en groupe · Départ d’Oujda',
                    'price_mad' => 199,
                    'program' => [
                        ['day' => 'Matin', 'time' => '08:00', 'title' => 'Départ d’Oujda', 'detail' => 'Rendez-vous au point de départ et route vers Saïdia.'],
                        ['day' => 'Matinée', 'time' => '10:00', 'title' => 'Plage et Méditerranée', 'detail' => 'Temps libre pour profiter du bord de mer.'],
                        ['day' => 'Après-midi', 'time' => '14:00', 'title' => 'Marina de Saïdia', 'detail' => 'Promenade et pause libre à la marina.'],
                        ['day' => 'Soir', 'time' => '17:00', 'title' => 'Retour vers Oujda', 'detail' => 'Départ du groupe après la journée.'],
                    ],
                    'sort_order' => 1,
                ],
                'nador-marchica' => [
                    'title' => 'Nador & Marchica',
                    'category' => 'Nature & lagune',
                    'summary' => 'Cap sur la lagune, ses panoramas et l’ambiance du littoral de l’Oriental.',
                    'badge' => 'Une journée',
                    'duration' => '1 jour',
                    'includes' => 'Transport aller-retour en groupe · Temps libre',
                    'price_mad' => 249,
                    'program' => [
                        ['day' => 'Matin', 'time' => '07:30', 'title' => 'Départ d’Oujda', 'detail' => 'Route en direction de Nador et de la lagune.'],
                        ['day' => 'Matinée', 'time' => '10:30', 'title' => 'Panorama de Marchica', 'detail' => 'Découverte des points de vue de la lagune.'],
                        ['day' => 'Après-midi', 'time' => '13:00', 'title' => 'Pause à Nador', 'detail' => 'Temps libre pour déjeuner et profiter du front de mer.'],
                        ['day' => 'Soir', 'time' => '17:00', 'title' => 'Retour vers Oujda', 'detail' => 'Départ du groupe pour le retour.'],
                    ],
                    'sort_order' => 2,
                ],
                'fes' => [
                    'title' => 'Week-end à Fès',
                    'category' => 'Culture & médina',
                    'summary' => 'Deux jours pour flâner dans la médina, goûter aux spécialités et changer d’air.',
                    'badge' => '2 jours · 1 nuit',
                    'duration' => '2 jours · 1 nuit',
                    'includes' => 'Transport A/R · 1 nuit · Petit-déjeuner · Chambre double',
                    'price_mad' => 890,
                    'program' => [
                        ['day' => 'Jour 1 · Matin', 'time' => '08:00', 'title' => 'Départ et arrivée à Fès', 'detail' => 'Trajet depuis Oujda puis installation selon les horaires de l’hébergement.'],
                        ['day' => 'Jour 1 · Après-midi', 'time' => '14:00', 'title' => 'Premiers pas dans la médina', 'detail' => 'Temps libre pour découvrir les ruelles et les monuments emblématiques.'],
                        ['day' => 'Jour 2 · Matin', 'time' => '09:00', 'title' => 'Saveurs et artisanat', 'detail' => 'Matinée libre pour les marchés, les ateliers et les spécialités locales.'],
                        ['day' => 'Jour 2 · Après-midi', 'time' => '15:00', 'title' => 'Retour vers Oujda', 'detail' => 'Rendez-vous du groupe et trajet retour.'],
                    ],
                    'sort_order' => 3,
                ],
                default => [
                    'title' => 'Marrakech découverte',
                    'category' => 'Grand circuit',
                    'summary' => 'La ville rouge, ses jardins et ses places emblématiques, le temps d’une vraie parenthèse.',
                    'badge' => '3 jours · 2 nuits',
                    'duration' => '3 jours · 2 nuits',
                    'includes' => 'Transport A/R · 2 nuits · Petits-déjeuners · Chambre double',
                    'price_mad' => 2190,
                    'program' => [
                        ['day' => 'Jour 1 · Matin', 'time' => '07:00', 'title' => 'Départ pour Marrakech', 'detail' => 'Départ du groupe depuis Oujda pour rejoindre la ville rouge.'],
                        ['day' => 'Jour 1 · Soir', 'time' => '18:00', 'title' => 'Jemaa el-Fna', 'detail' => 'Première découverte de la place et installation à l’hébergement.'],
                        ['day' => 'Jour 2 · Matin', 'time' => '09:00', 'title' => 'Jardins et patrimoine', 'detail' => 'Temps libre pour les jardins et les sites historiques.'],
                        ['day' => 'Jour 2 · Après-midi', 'time' => '14:00', 'title' => 'Médina et souks', 'detail' => 'Promenade libre dans les quartiers et marchés de la médina.'],
                        ['day' => 'Jour 3 · Matin', 'time' => '09:00', 'title' => 'Derniers instants à Marrakech', 'detail' => 'Matinée libre avant le rendez-vous de départ.'],
                        ['day' => 'Jour 3 · Après-midi', 'time' => '13:00', 'title' => 'Retour vers Oujda', 'detail' => 'Départ du groupe pour le retour.'],
                    ],
                    'sort_order' => 4,
                ],
            };

            Offer::query()->updateOrCreate(
                ['destination_id' => $destination->id, 'title' => $offer['title']],
                [...$offer, 'destination_id' => $destination->id, 'is_active' => true]
            );

            $savedOffer = Offer::query()
                ->where('destination_id', $destination->id)
                ->where('title', $offer['title'])
                ->firstOrFail();
            $weekday = match ($destination->slug) {
                'saidia' => 6,
                'nador-marchica' => 0,
                'fes' => 5,
                default => 4,
            };
            $durationDays = match ($destination->slug) {
                'fes' => 1,
                'marrakech' => 2,
                default => 0,
            };

            $today = CarbonImmutable::today();
            for ($monthOffset = 0; $monthOffset < 6; $monthOffset++) {
                $monthStart = $today->startOfMonth()->addMonths($monthOffset);
                $searchStart = $monthOffset === 0 ? $today : $monthStart;
                $departureDate = $searchStart->addDays(($weekday - $searchStart->dayOfWeek + 7) % 7);

                $returnDate = $departureDate->addDays($durationDays);
                Departure::query()->updateOrCreate(
                    ['offer_id' => $savedOffer->id, 'departure_date' => $departureDate->toDateString()],
                    [
                        'return_date' => $returnDate->toDateString(),
                        'status' => 'open',
                        'note' => 'Date proposée — départ et disponibilité à confirmer par l’agence.',
                    ]
                );
            }
        }
    }
}
