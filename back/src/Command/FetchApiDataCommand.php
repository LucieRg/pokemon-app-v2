<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\PokeApiService;
use App\Entity\Pokemon;

class FetchApiDataCommand extends Command
{
    protected function configure(){
        $this
            ->setDescription('Fetch Pokémon data from the API and store them in the database')
            ->setName('app:fetch-api-data');
    }

private PokeApiService $pokeApiService;
    private EntityManagerInterface $entityManager;

    public function __construct(PokeApiService $pokeApiService, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->pokeApiService = $pokeApiService;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Fetching Pokémon data from the API...');

        $pokemons = $this->pokeApiService->getPokemons();

        foreach ($pokemons as $pokemonData) {
            $pokemonName = $pokemonData->getName();
            $output->writeln("Processing Pokémon: $pokemonName");

            $existingPokemon = $this->entityManager->getRepository(Pokemon::class)->findOneBy(['name' => $pokemonName]);

            if (!$existingPokemon) {
                $pokemon = new Pokemon();
                $pokemon->setName($pokemonName);
                $pokemon->setHeight($pokemonData->getHeight());
                $pokemon->setWeight($pokemonData->getWeight());

                $pokemon->setImageUrl($pokemonData['imageUrl']);
                $pokemon->setTypes($pokemonData->getTypes());
                $abilities = [];
                if ($pokemonData->getAbilities()) {
                    $abilities = array_map(function($abilityData) {
                        return $abilityData['ability']['name'];
                    }, $pokemonData->getAbilities());
                }
                $pokemon->setAbilities($abilities);

                $this->pokeApiService->addPokemon($pokemon);
                $output->writeln("Added Pokémon: $pokemonName");
            } else {
                $output->writeln("Pokémon already exists: $pokemonName");
            }
        }

        $output->writeln('Pokémon data fetching and storage completed!');

        return Command::SUCCESS;
    }



}
