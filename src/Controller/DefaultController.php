<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\AnnoncePicture;
use App\Entity\User;
use App\services\AdService;
use App\services\AnnonceService;
use App\services\CategoryService;
use App\services\ExampleService;
use App\Type\AnnonceType;
use App\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class DefaultController extends AbstractController
{
    #[Route('/', name: 'app.home')]
    #[Route('/page/{page}', name: 'app.home.page')]
    public function index(
        Request         $request,
        AnnonceService  $annonceService,
        CategoryService $categoryService,
        int             $page = 1
    ): Response
    {
        //$page = (int)$request->get('page', 1);

        $limit = (int)$request->get('limit', 5);

        $filters = [];

        if ($request->get('query') !== null) {
            $filters['query'] = $request->get('query');
        }

        if ($request->get('categories') !== null && is_array($request->get('categories'))) {

            $filters['in_categories'] = $request->get('categories');
        }

        if ($request->get('price_sup') !== null) {
            $filters['price_sup'] = (int)$request->get('price_sup');
        }

        if ($request->get('price_inf') !== null) {
            $filters['price_inf'] = (int)$request->get('price_inf');
        }

        $order = [];
        $allowedOrder = ['price', 'title', 'postedDate', 'rating'];
        if ($request->get('order') !== null && str_contains($request->get('order'), ',')) {
            $o_ = explode(',', $request->get('order'));
            if (in_array($o_[0], $allowedOrder, true)) {
                $order[$o_[0]] = strtoupper($o_[1]);
            }
        }

        try {
            $annonces = $annonceService->getAnnonces($filters, $order, $page, $limit);
        } catch (\Throwable $e) {
            if ($e->getCode() === 10) {
                // page does not exists
                throw $this->createNotFoundException('La page n\'existe pas !');
            }
            $annonces = [
                'results' => [],
                'count' => 0,
                'totalPages' => 1,
                'error' => $e->getMessage(),
            ];
        }
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'annonceQuery' => $annonces,
            'queryParams' => http_build_query($_GET),
            'actualPage' => $page,
            'categories' => $categoryService->getAllCategories(),
        ]);
    }

    #[Route('/register', name: 'app.register')]
    public function addUser(
        EntityManagerInterface      $em,
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher,
    ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('newsletter')->getData() === true) {
                $user->setNewsletter(true);
            } else {
                $user->setNewsletter(false);
            }
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
            $user->setRoles(['ROLE_USER']);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app.home');
        }
        return $this->render('default/register.html.twig', [
            'formAddUser' => $form->createView(),
        ]);
    }

    #[Route('/annonce/add/', name: 'app.add')]
    public function addAnnonce(
        EntityManagerInterface $em,
        Request                $request,
    ): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('You need to be logged in!');
        }

        $annonce = new Annonce(
            'title',
            'description',
            'price',
            'address',
        );
        $annonce->setOwner($user);
        $annonce->setPostedDate(new \DateTime());

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        $picture = new AnnoncePicture(
            ''
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($annonce);
            $em->flush();
            return $this->redirectToRoute('app.home');
        }

        return $this->render('default/add.annonce.html.twig', [
            'formulaireAddAnnonce' => $form->createView(),
        ]);
    }

    #[Route('/annonce/{id}', name: 'ads.display.simple', requirements: ['id' => '^\d+'])]
    public function displaySimple(
        ExampleService         $exampleService,
        EntityManagerInterface $em,
        AdService              $adService,
        int                    $id
    ): Response
    {
        $seller = $exampleService->getSeller();
        $annonce = $em->getRepository(Annonce::class)
            ->findOneBy(['id' => $id]);
        if (!$annonce instanceof Annonce) {
            throw new NotFoundHttpException('Annonce does not exist');
        }
        $advertisement = $adService->getAds();

        return $this->render('default/ad.display.html.twig', [
            'controller_name' => 'DefaultController',
            'seller' => $seller,
            'ad1' => $advertisement[0],
            'ad2' => $advertisement[1],
            'annonce' => $annonce,
        ]);
    }

    #[Route('/annonce/{cat}/{id}', name: 'ads.display', requirements: ['id' => '^\d+', 'cat' => '[a-z][a-z0-9_-]+'])]
    public function display(string $cat, int $id): Response
    {
        return $this->render('default/ad.display.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
