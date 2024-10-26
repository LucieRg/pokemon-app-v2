<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Pokemon;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PokemonController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route('/pokemon/add', name: 'pokemon_add', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $pokemon = new Pokemon();
        $this->handlePokemonData($request, $pokemon);

        $errors = $this->validator->validate($pokemon);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($pokemon);
        $this->entityManager->flush();

        return $this->json(['message' => 'Pokémon ajouté avec succès !'], Response::HTTP_CREATED);
    }
    #[Route('/pokemon/{id}', name: 'pokemon_get', methods: ['GET'])]
    public function getPokemon(int $id): Response
    {
        $pokemon = $this->entityManager->getRepository(Pokemon::class)->find($id);

        if (!$pokemon) {
            return $this->json(['error' => 'Pokémon non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $pokemon->getId(),
            'name' => $pokemon->getName(),
            'height' => $pokemon->getHeight(),
            'weight' => $pokemon->getWeight(),
            'types' => $pokemon->getTypes(),
            'abilities' => $pokemon->getAbilities(),
            'imageUrl' => $pokemon->getImageUrl(),
        ]);
    }

    #[Route('/pokemon/edit/{id}', name: 'pokemon_edit', methods: ['PUT'])]
    public function edit(Request $request, int $id): Response
    {
        $pokemon = $this->entityManager->getRepository(Pokemon::class)->find($id);

        if (!$pokemon) {
            return $this->json(['error' => 'Pokémon non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $this->handlePokemonData($request, $pokemon);

        $errors = $this->validator->validate($pokemon);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();
        return $this->json(['message' => 'Pokémon mis à jour avec succès !']);
    }

    #[Route('/pokemon/delete/{id}', name: 'pokemon_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $pokemon = $this->entityManager->getRepository(Pokemon::class)->find($id);

        if ($pokemon) {
            $this->entityManager->remove($pokemon);
            $this->entityManager->flush();
            return $this->json(['message' => 'Pokémon supprimé avec succès !']);
        } else {
            return $this->json(['error' => 'Pokémon non trouvé.'], Response::HTTP_NOT_FOUND);
        }
    }

    private function handlePokemonData(Request $request, Pokemon $pokemon): void
    {
        $data = json_decode($request->getContent(), true);

        $pokemon->setName($data['name'] ?? null);
        $pokemon->setHeight((int)($data['height'] ?? 0));
        $pokemon->setWeight((int)($data['weight'] ?? 0));
        $pokemon->setTypes($data['types'] ?? []);
        $pokemon->setAbilities($data['abilities'] ?? []);
        $pokemon->setImageUrl($data['imageUrl'] ?? null);
    }


}
