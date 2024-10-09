<?php

namespace App\Controller;

use App\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserRoleController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/user_roles", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $roles = $this->entityManager->getRepository(UserRole::class)->findAll();
        $data = array_map(function (UserRole $role) {
            return [
                'id' => $role->getId(),
                'name' => $role->getName(),
                'userCount' => count($role->getUsers())
            ];
        }, $roles);

        return $this->json($data);
    }

    /**
     * @Route("/user_roles/{id}", methods={"GET"})
     */
    public function detail(int $id): JsonResponse
    {
        $role = $this->entityManager->getRepository(UserRole::class)->find($id);
        if (!$role) {
            return $this->json(['message' => 'Role not found'], 404);
        }
        $data = [
            'id' => $role->getId(),
            'name' => $role->getName(),
            'userCount' => count($role->getUsers())
        ];
        return $this->json($data);
    }

    /**
     * @Route("/user_roles", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        // Implement role creation logic here
        return $this->json(['message' => 'Role created'], 201);
    }

    /**
     * @Route("/user_roles/{id}", methods={"PUT"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        // Implement role update logic here
        return $this->json(['message' => 'Role updated']);
    }

    /**
     * @Route("/user_roles/{id}", methods={"PATCH"})
     */
    public function partialUpdate(int $id, Request $request): JsonResponse
    {
        // Implement partial update logic here
        return $this->json(['message' => 'Role partially updated']);
    }

    /**
     * @Route("/user_roles/{id}", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        // Implement role deletion logic here
        return $this->json(['message' => 'Role deleted']);
    }
}
