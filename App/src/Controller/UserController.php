<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/users", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        return $this->json($users, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/users/{id}", methods={"GET"})
     */
    public function detail(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json(['message' => 'User not found'], 404);
        }
        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/users", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        // Implement user creation logic here
        return $this->json(['message' => 'User created'], 201);
    }

    /**
     * @Route("/users/{id}", methods={"PUT"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        // Implement user update logic here
        return $this->json(['message' => 'User updated']);
    }

    /**
     * @Route("/users/{id}", methods={"PATCH"})
     */
    public function partialUpdate(int $id, Request $request): JsonResponse
    {
        // Implement partial update logic here
        return $this->json(['message' => 'User partially updated']);
    }

    /**
     * @Route("/users/{id}", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        // Implement user deletion logic here
        return $this->json(['message' => 'User deleted']);
    }
}
