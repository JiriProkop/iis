<?php

namespace App\Controller;

use PHPUnit\Framework\Constraint\IsEmpty;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\PersonEntity;
use App\Form\LoginFormType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\PersonEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Environment;




class LoginController extends AbstractController
{
    private PersonEntityRepository $personRepository;
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;
    private $twig;

    public function __construct(PersonEntityRepository $personEntityRepository, EntityManagerInterface $em, TokenStorageInterface $tokenStorage, Environment $twig)
    {
        $this->personRepository = $personEntityRepository;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error' => $error,
        ]);

    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \Exception('This should not be reached!');
    }

}
