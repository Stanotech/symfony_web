<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;
use App\Entity\User;

class PostController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route("/api/posts/search", name: "search_posts", methods: ["GET"])]
    public function searchAndSort(Request $request, PostRepository $postRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $searchTerm = $data['searchTerm'];
        $sortField = $data['sortField'];
        $sortOrder = $data['sortOrder'];

        $posts = $postRepository->searchAndSort($searchTerm, $sortField, $sortOrder);

        return $this->json($posts, 200, [], ['groups' => 'post:read']);
    }

    #[Route("/api/posts", methods: ["GET"])]
    public function list(): JsonResponse
    {
        $posts = $this->entityManager->getRepository(Post::class)->findAll();
        return $this->json($posts, 200, [], ['groups' => 'post:read']);
    }

    #[Route("/api/posts/{id}", methods: ["GET"])]
    public function detail(int $id): JsonResponse
    {
        $post = $this->entityManager->getRepository(Post::class)->find($id);
        if (!$post) {
            return $this->json(['message' => 'Post not found'], 404);
        }
        return $this->json($post, 200, [], ['groups' => 'post:read']);
    }

    #[Route("/api/posts", methods: ["POST"])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $post = new Post();
        $post->setTitle($data['title']);
        $post->setContent($data['body']);
        $post->setCreatedAt(new \DateTime());
        $post->setAuthor($this->getUser());

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $this->json(['message' => 'Post created'], 201);
    }

    #[Route("/api/posts/{id}", methods: ["PUT"])]
    public function update(int $id, Request $request): JsonResponse
    {
        $post = $this->entityManager->getRepository(Post::class)->find($id);
        if (!$post) {
            return $this->json(['message' => 'Post not found'], 404);
        }
        $data = json_decode($request->getContent(), true);
        $post->setTitle($data['title']);
        $post->setContent($data['body']);
        $post->setUpdatedAt(new \DateTime());
        $post->setAuthor($this->getUser());

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $this->json(['message' => 'Post updated']);
    }

    #[Route("/api/posts/{id}", methods: ["PATCH"])]
    public function partialUpdate(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $post = $this->entityManager->getRepository(Post::class)->find($id);
        if (!$post) {
            return $this->json(['message' => 'Post not found'], 404);
        }

        if ($post->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['message' => 'Access denied'], 403);
        }

        if (isset($data['title'])) {
            $post->setTitle($data['title']);
        }

        if (isset($data['body'])) {
            $post->setContent($data['body']);
        }

        $this->entityManager->persist($post);
        $this->entityManager->flush();
        return $this->json(['message' => 'Post partially updated']);
    }

    #[Route("/api/posts/{id}", methods: ["DELETE"])]
    public function delete(int $id): JsonResponse
    {
        $post = $this->entityManager->getRepository(Post::class)->find($id);
        if (!$post) {
            return $this->json(['message' => 'Post not found'], 404);
        }

        if ($post->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['message' => 'Access denied'], 403);
        }
        
        $this->entityManager->remove($post);
        $this->entityManager->flush();
        return $this->json(['message' => 'Post deleted']);
    }
}
