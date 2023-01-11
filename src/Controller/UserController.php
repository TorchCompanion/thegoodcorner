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


class UserController extends AbstractController
{
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
}
