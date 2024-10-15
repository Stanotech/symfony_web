<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\UserRole;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route("/api/users", methods: ["GET"])]
    public function list(): JsonResponse
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        return $this->json($users, 200, [], ['groups' => 'user:read']);
    }

    #[Route("/api/users/{id}", methods: ["GET"])]
    public function detail(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json(['message' => 'User not found'], 404);
        }
        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    #[Route("/api/users", methods: ["POST"])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password']) || !isset($data['role']) || !isset($data['first_name']) || !isset($data['last_name'])) {
            return $this->json(['error' => 'Missing required fields'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $role = $this->entityManager->getRepository(UserRole::class)->findOneBy(['name' => $data['role']]);
    
        if ($role) {
            $user->addRole($role);
        } else {
            return $this->json(['error' => 'Role not found'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user->setFirstName($data['first_name'] ?? '');
        $user->setLastName($data['last_name'] ?? '');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User created'], 201);
    }

    #[Route("/api/users/{id}", methods: ["PUT"])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['first_name'], $data['last_name'], $data['password'], $data['role'])) {
            return $this->json(['error' => 'All fields are required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $role = $this->entityManager->getRepository(UserRole::class)->findOneBy(['name' => $data['role']]);
        $user->eraseCredentials();

        $user->setEmail($data['email']);
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->addRole($role);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User completely updated']);
    }

    #[Route("/api/users/{id}", methods: ["PATCH"])]
    public function partialUpdate(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['first_name'])) {
            $user->setFirstName($data['first_name']);
        }

        if (isset($data['last_name'])) {
            $user->setLastName($data['last_name']);
        }

        if (isset($data['password'])) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User updated']);
    }

    #[Route("/api/users/{id}", methods: ["DELETE"])]
    public function delete(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json(['message' => 'User not found'], 404);
        }
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return $this->json(['message' => 'User deleted']);
    }
}
