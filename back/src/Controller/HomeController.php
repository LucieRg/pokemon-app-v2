<?php

namespace App\Controller;

use App\Entity\Pokemon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PokeApiService;

class HomeController extends AbstractController
{
    private PokeApiService $pokeApiService;
    private EntityManagerInterface $entityManager;

    public function __construct(PokeApiService $pokeApiService, EntityManagerInterface $entityManager)
    {
        $this->pokeApiService = $pokeApiService;
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        $pokemons = $this->entityManager->getRepository(Pokemon::class)->findBy([], ['name' => 'ASC']);
        return $this->json($pokemons);
    }

    #[Route('/pokemon/search', name: 'pokemon_search', methods: ['POST'])]
    public function search(Request $request): Response
    {
        $searchTerm = $request->request->get('pokemonNameOrId');
        $pokemonRepository = $this->entityManager->getRepository(Pokemon::class);
        $pokemons = $pokemonRepository->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->setParameter('name', $searchTerm . '%')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();

        if (empty($pokemons)) {
            return $this->json(['message' => 'Aucun Pokémon trouvé.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($pokemons);
    }
}
