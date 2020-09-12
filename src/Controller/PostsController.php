<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
	/**
	 * @var PostRepository $postRepository
	 */
	private $postRepository;

	public function __construct(PostRepository $postRepository)
	{
		$this->postRepository = $postRepository;
	}

    /**
     * @Route("/posts", name="posts")
     */
    public function index()
    {
		$posts = $this->postRepository->findAll();

		return $this->render('posts/index.html.twig', [
			'posts' => $posts,
		]);
    }

	/**
	 * @Route("/posts/{slug}", name="post_show")
	 */
    public function post(Post $post)
	{
		return $this->render('posts/show.html.twig', [
			'post' => $post,
		]);
	}
}
