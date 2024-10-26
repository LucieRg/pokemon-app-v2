<?php

namespace App\Service;

use App\Entity\Pokemon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PokeApiService
{
    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function getPokemons(): array
    {
        $pokemons = [];
        try {
            $response = $this->client->request('GET', 'https://pokeapi.co/api/v2/pokemon?limit=100');
            $data = $response->toArray();

            foreach ($data['results'] as $pokemonData) {
                $pokemonDetails = $this->fetchPokemonData($pokemonData['name']);
                if ($pokemonDetails) {
                    $pokemons[] = $this->createPokemonEntity($pokemonData, $pokemonDetails);
                }
            }

            foreach ($pokemons as $pokemonEntity) {
                $this->entityManager->persist($pokemonEntity);
            }
            $this->entityManager->flush();

        } catch (TransportExceptionInterface $e) {
            $this->logger->error("Error fetching Pokémon: " . $e->getMessage());
        }

        return $pokemons;
    }

    private function fetchPokemonData(string $name): array
    {
        try {
            $response = $this->client->request('GET', 'https://pokeapi.co/api/v2/pokemon/' . $name);
            return $response->toArray();
        } catch (TransportExceptionInterface $e) {
            $this->logger->error("Error fetching Pokémon data for $name: " . $e->getMessage());
            return [];
        }
    }
    private function createPokemonEntity(array $pokemonData, array $pokemonDetails): Pokemon
    {
        $pokemonEntity = new Pokemon();
        $pokemonEntity->setName($pokemonData['name']);
        $pokemonEntity->setHeight($pokemonDetails['height'] ?? null);
        $pokemonEntity->setWeight($pokemonDetails['weight'] ?? null);
        $sprites = $pokemonDetails['sprites'] ?? [];
        $pokemonEntity->setFrontDefault($sprites['front_default'] ?? null);
        $pokemonEntity->setBackDefault($sprites['back_default'] ?? null);
        $types = array_map(fn($type) => $type['type']['name'], $pokemonDetails['types'] ?? []);
        $pokemonEntity->setTypes($types);
        $abilities = array_map(fn($ability) => $ability['ability']['name'], $pokemonDetails['abilities'] ?? []);
        $pokemonEntity->setAbilities($abilities);

        return $pokemonEntity;
    }


    public function addPokemon(Pokemon $pokemon): void
    {
        $this->entityManager->persist($pokemon);
        $this->entityManager->flush();
    }

}
