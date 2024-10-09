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
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $role = $data['role'] ?? '';
        $firstName = $data['first_name'] ?? '';
        $lastName = $data['last_name'] ?? '';

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setRole($role);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User created'], 201);
    }

    /**
     * @Route("/users/{id}", methods={"PUT"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // necessary data exists ?
        if (!isset($data['firstName']) || !isset($data['lastName']) || !isset($data['email'])) {
            return $this->json(['error' => 'Invalid data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Get user
        $user = $this->entityManager->getRepository(User::class)->find($id);

        // Check if exists
        if (!$user) {
            return $this->json(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // update data
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);

        // prepare and save
        $this->entityManager->persist($user);
        $this->entityManager->flush();

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
