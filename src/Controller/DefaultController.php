<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\CommentType;
use Cocur\Slugify\Slugify;
use App\Service\Calculator;
use Doctrine\ORM\EntityManager;
use App\Service\ArticleValidator;
use App\Service\MarkdownCacheHelper;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class DefaultController extends AbstractController {

    /**
     * @Route("/", name="home")
     */
    public function index(ArticleRepository $repo) {
        $articles = $repo->findPublishedArticles();

        return $this->render('article/home.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/news/{slug}", name="article_show")
     */
    public function show(ObjectManager $manager, Article $article, MarkdownCacheHelper $markdownHelper, Request $request) {

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setCreatedAt(new \DateTime()); // plutot utiliser un lifecycle event avec un prepersist

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        $articleContent = $markdownHelper->parse($article->getContent());

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'articleContent' => $articleContent,
            'commentForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/article/new", name="new_article")
     * @IsGranted("ROLE_ADMIN")
     */
    public function form(Request $request, ObjectManager $manager) {

            // annnotation ou $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
        $article = new Article();

        $article->setTitle('Nicolas sarkozy en garde à vue')
            ->setImage('https://placehold.it/400x200');

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class)
            ->add('image', UrlType::class, ['default_protocol' => null]) // N'ajoutera pas de http:// si on l'oublie
            ->add('content', TextareaType::class)
            ->add('categories', EntityType::class, [
                'label' => 'Catégories',
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($article);

//            foreach ($article->getCategories() as $category) {
//                $category->addArticle($article);
//            }

            $manager->flush();
        }

        return $this->render('article/create.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }
}
