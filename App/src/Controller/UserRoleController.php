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
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'])) {
            return $this->json(['error' => 'Missing required field'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $role = $this->entityManager->getRepository(UserRole::class)->findOneBy(['name' => $data['name']]);
        if ($role) {
            return $this->json(['error' => 'Role already exists'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $role = new UserRole();
        $role->setName($data['name']);

        $this->entityManager->persist($role);
        $this->entityManager->flush();

        return $this->json(['message' => 'Role created'], 201);
    }

    /**
     * @Route("/user_roles/{id}", methods={"PUT"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $role = $this->entityManager->getRepository(UserRole::class)->find($id);
        if (!$role) {
            return $this->json(['message' => 'Role not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!isset($data['name'])) {
            return $this->json(['error' => 'Missing required field'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $role->setName($data['name']);

        $users = $role->getUsers();
        foreach ($users as $user) {
            $role->removeUser($user);
        }

        $this->entityManager->persist($role);
        $this->entityManager->flush();

        return $this->json(['message' => 'Role updated']);
    }

    /**
     * @Route("/user_roles/{id}", methods={"PATCH"})
     */
    public function partialUpdate(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $role = $this->entityManager->getRepository(UserRole::class)->find($id);
        if (!$role) {
            return $this->json(['message' => 'Role not found'], 404);
        }

        if (isset($data['name'])) {
            $role->setName($data['name']);
        }

        $this->entityManager->persist($role);
        $this->entityManager->flush();

        return $this->json(['message' => 'Role partially updated']);
    }

    /**
     * @Route("/user_roles/{id}", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $role = $this->entityManager->getRepository(UserRole::class)->find($id);
        if (!$role) {
            return $this->json(['message' => 'Role not found'], 404);
        }
        
        $this->entityManager->remove($role);
        $this->entityManager->flush();

        return $this->json(['message' => 'Role deleted']);
    }
}
