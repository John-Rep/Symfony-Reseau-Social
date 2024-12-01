<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post')]
final class PostController extends AbstractController{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, UserRepository $userRepository, CommentRepository $commentRepository): Response
    {
        $posts = $postRepository->findAll();
        foreach ($posts as $post) {
            $post->setAuthor($userRepository->findOneById($post->getUserId())->getUsername());
            $comments = $commentRepository->findBy(['post_id' => $post->getId()]);
            foreach ($comments as $comment) {
                if ($comment->getUserId()) {
                    $comment->setAuthor($userRepository->findOneById($comment->getUserId())->getUsername());
                } else {
                    $comment->setAuthor('Anonymous User');
                }
                
            }
            $post->setComments($comments);
            
        }
        rsort($posts);
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($this->getUser()) {
            $user = $this->getUser()->getUsername();
        } else {
            $user = 'Anonymous User';
        }

        if ($form->isSubmitted() && $form->isValid()) {
            
            $post->setDate(date_create());
            $post->setUserId($this->getUser()->getId());
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager, CommentRepository $commentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            $comments = $commentRepository->findBy(['post_id' => $post->getId()]);
            foreach ($comments as $comment) {
                $entityManager->remove($comment);
            }
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
