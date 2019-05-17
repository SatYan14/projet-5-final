<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Question;
use App\Form\CommentType;
use App\Form\QuestionType;
use App\Entity\CommentLike;
use App\Entity\QuestionLike;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentLikeRepository;
use App\Repository\QuestionLikeRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{



    /**
     * @Route("/", name="home")
     */
    public function home(EntityManagerInterface $manager, Request $request, QuestionRepository $repo, CategoryRepository $categoryRepo)
    {


        $form = $this->createForm(QuestionType::class);
        $questions = $repo->findAll();
        $category = $categoryRepo->findAll();
        $user = $this->getUser();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user == null) {
                return $this->redirectToRoute('app_login');
            };
            $question = new Question();
            $question = $form->getData();
            $question->setUser($user);
            $manager->persist($question);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('app/home.html.twig', [
            'questions' => $questions,
            'category' => $category,
            'form' => $form->createView(),
            'user' => $user
        ]);
    }


    /**
     * @Route("/category/{id_category}", name="category")
     */
    public function goToCategory($id_category, QuestionRepository $questionRepository)
    {
        $user = $this->getUser();
        $questions = $questionRepository->findBy([
            'category' => $id_category
        ]);

        return $this->render('app/category.html.twig', [
            'questions' => $questions,
            'user' => $user
        ]);
    }

    /**
     * @Route("/question/{id}", name="show")
     */
    public function show($id, EntityManagerInterface $manager, Request $request, Question $question, CategoryRepository $categoryRepo, CommentRepository $commentRepo)
    {

        // ParamConverter permet de voir qu'on a besoin d'un id et d'un article, il en déduit
        // donc la question concernée.

        $comments = $commentRepo->orderComments($id);


        $form = $this->createForm(CommentType::class);
        $user = $this->getUser();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user == null) {
                return $this->redirectToRoute('app_login');
            }
            $comment = new Comment();
            $comment = $form->getData();
            $comment->createComment($question, $user);
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('show', [
                'id' => $question->getId()
            ]);
        }

        $category = $categoryRepo->findAll();
        return $this->render('app/show.html.twig', [
            'question' => $question,
            'category' => $category,
            'form' => $form->createView(),
            'user' => $user,
            'comments' => $comments
        ]);
    }


    /**
     * @Route("/hot", name="hot")
     */
    public function hot(QuestionRepository $repo, CategoryRepository $categoryRepo)
    {
        $questions = $repo->getReallyHotQuestion();
        $category = $categoryRepo->findAll();
        $user = $this->getUser();
        return $this->render('app/hot.html.twig', [
            'questions' => $questions,
            'category' => $category,
            'user' => $user
        ]);
    }

    /**
     * @Route("/profil/{id}", name="profil")
     */
    public function profil($id, User $user, CategoryRepository $categoryRepo, UserRepository $userRepo)
    {
        $user = $userRepo->findOneBy([
            'id' => $id
        ]);
        $category = $categoryRepo->findAll();

        return $this->render('app/profil.html.twig', [
            'category' => $category,
            'user' => $user
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \RuntimeException('Cela ne doit pas être appelé directement');
    }

    /**
     * @Route("/fresh", name="fresh")
     */
    public function fresh(QuestionRepository $repo, CategoryRepository $categoryRepo)
    {
        $questions = $repo->getNewsQuestion();
        $category = $categoryRepo->findAll();
        $user = $this->getUser();
        return $this->render('app/fresh.html.twig', [
            'questions' => $questions,
            'category' => $category,
            'user' => $user
        ]);
    }

    /**
     * @Route("/question/{id}/like", name="question_like")
     * 
     * @param \App\Entity\Question $question
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param \App\Repository\QuestionLikeRepository $likeRepo
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function like(Question $question, ObjectManager $manager, QuestionLikeRepository $likeRepo): Response
    {
        $user = $this->getUser();

        if (!$user) return $this->json([
            'code' => 403,
            'message' => "Unauthorized"
        ], 403);

        if ($question->isLikedByUser($user)) {
            $like = $likeRepo->findOneBy([
                'question' => $question,
                'user' => $user
            ]);

            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Like bien supprimé',
                'likes' => $likeRepo->count(['question' => $question])
            ], 200);
        }

        $like = new QuestionLike();
        $like->setQuestion($question)
            ->setUser($user);

        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'like bien ajouté',
            'likes' => $likeRepo->count(['question' => $question])
        ], 200);
    }

    /**
     * @Route("comment/{id}/like", name="comment_like")
     */
    public function commentLike(Comment $comment, ObjectManager $manager, CommentLikeRepository $likeRepo): Response
    {
        $user = $this->getUser();

        if (!$user) return $this->json([
            'code' => 403,
            'message' => "Unauthorized"
        ], 403);

        if ($comment->isLikedByUser($user)) {
            $like = $likeRepo->findOneBy([
                'comment' => $comment,
                'user' => $user
            ]);

            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Like bien supprimé',
                'likes' => $likeRepo->count(['comment' => $comment])
            ], 200);
        }

        $like = new CommentLike();
        $like->setComment($comment)
            ->setUser($user);

        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'like bien ajouté',
            'likes' => $likeRepo->count(['comment' => $comment])
        ], 200);
    }
}
