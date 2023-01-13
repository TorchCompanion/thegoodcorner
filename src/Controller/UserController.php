<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\AnnoncePicture;
use App\Entity\ProfilePicture;
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
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class UserController extends AbstractController
{
    #[Route('/register', name: 'app.register')]
    public function addUser(
        EntityManagerInterface      $em,
        Request                     $request,
        KernelInterface             $kernel,
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
            $picture = $form->get('picture')->getData();
            if ($picture !== null && $picture instanceof UploadedFile) {
                $ext = $picture->guessExtension();
                $ds = DIRECTORY_SEPARATOR;
                $filename = uniqid('media_', true) . '.' . $ext;
                $webPath = 'uploads';
                $storagePath = $kernel->getProjectDir() . $ds . 'public' . $ds . $webPath;
                $absolutePath = $storagePath . $ds . $filename;
                $picture->move($storagePath, $filename);

                $picture = new ProfilePicture();
                $picture->setFilename($filename);
                $picture->setAbsolutePath($absolutePath);
                $picture->setWebPath('/' . $webPath . '/');
                $em->persist($picture);
                $user->setProfilePicture($picture);
            }
            $user->setRoles(['ROLE_USER']);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app.home');
        }
        return $this->render('default/register.html.twig', [
            'formAddUser' => $form->createView(),
        ]);
    }

    #[Route('/profil/{id}', name: 'user.profil', requirements: ['id' => '^\d+'])]
    public function displayUser(
        EntityManagerInterface $em,
        AdService              $adService,
        AnnonceService         $annonceService,
        Request                $request,
        int                    $id
    ): Response
    {
        $user = $em->getRepository(User::class)
            ->findOneBy(['id' => $id]);
        if (!$user instanceof User) {
            throw new NotFoundHttpException('User does not exist');
        }

        $qb = $em->createQueryBuilder();
        $qb
            ->select('a')
            ->from(Annonce::class, 'a')
            ->where('1 = 1')
            ->leftJoin(User::class, 'u')
            ->andWhere('a.owner = u.id');

        $annonce = $qb->getQuery()->getResult();
        $advertisement = $adService->getAds();

        return $this->render('default/user.display.html.twig', [
            'controller_name' => 'UserController',
            'ad1' => $advertisement[0],
            'ad2' => $advertisement[1],
            'user' => $user,
            'annonceQuery' => $annonce
        ]);
    }
}

