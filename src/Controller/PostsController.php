<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
	 * @Route("/posts/add", name="post_add")
	 */
	public function addPost(Request $request, Slugify $slugify)
	{
		$post = new Post();
		$form = $this->createForm(PostType::class, $post);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$post->setSlug($slugify->slugify($post->getTitle()));
			$post->setCreatedAt(new \DateTime());

			$em = $this->getDoctrine()->getManager();

			$em->persist($post);
			$em->flush();

			return $this->redirectToRoute('posts');
		}

		return $this->render('posts/form.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("posts/{slug}/edit", name="post_edit")
	 */
	public function editPost(Post $post, Request $request, Slugify $slugify)
	{
		$form = $this->createForm(PostType::class, $post);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$post->setSlug($slugify->slugify($post->getTitle()));

			$em = $this->getDoctrine()->getManager();

			$em->flush();

			return $this->redirectToRoute('post_show', [
				'slug' => $post->getSlug(),
			]);
		}

		return $this->render('posts/form.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("posts/{slug}/delete", name="post_delete")
	 */
	public function deletePost(Post $post)
	{
		$em = $this->getDoctrine()->getManager();

		$em->remove($post);
		$em->flush();

		return $this->redirectToRoute('posts');
	}

	/**
	 * @Route("/posts/search", name="post_search")
	 */
	public function searchPost(Request $request)
	{
		$query = $request->query->get('q');
		$posts = $this->postRepository->searchByQuery($query);

		return $this->render('/posts/search_results.html.twig', [
			'searchString' => $query,
			'posts' => $posts,
		]);
	}

	/**
	 * @Route("/posts/{slug}", name="post_show")
	 */
    public function showPost(Post $post)
	{
		return $this->render('posts/show.html.twig', [
			'post' => $post,
		]);
	}
}
