<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\MemberRepository;
use App\Repository\UserRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager,
        SendMailService $sendMailService, JWTService $jwt
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->getData()->getType() == 'organization')
                $user->setRoles(['ROLE_USER','ROLE_ORGANIZATION']);
            else if ($form->getData()->getType() == 'stackholder')
                $user->setRoles(['ROLE_USER','ROLE_STACKHOLDER']);
            else
                $user->setRoles(['ROLE_USER']);
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // Create JWT Header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // Create JWT Payload
            $payload = [
                'user_id' => $user->getId()
            ];

            // Generate JWT
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            // send email to user
            $sendMailService->send(
                'yasmine.souissi@esprit.tn',
                $user->getEmail(),
                'Welcome to GoalsLink',
                'register',
                compact('user', 'token')
            );
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'member' => [
                'name' => null,
                'lastname' => null,
                'email' => null,
            ]
        ]);
    }
    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        // check if token is valid and not expired 
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            // get payload from token to retrieve user id
            $payload = $jwt->getPayload($token);

            // Find user by id
            $user = $userRepository->find($payload['user_id']);

            // check if user exists and is not activated
            if ($user && !$user->getIsActivated()) {
                $user->setIsActivated(true);
                $em->flush();
                $this->addFlash('success', 'User activated successfully');
                return $this->redirectToRoute('app_home'); 
            }
        }
        // if token is invalid or expired or user is already activated flash message and redirect to login page
        $this->addFlash('danger', 'Invalid token or user already activated');
        return $this->redirectToRoute('app_login');
    }
    #[Route('/resendVerif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $sendMailService, UserRepository $usesrRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'You must be logged in to access this page');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getIsActivated()) {
            $this->addFlash('warning', 'Your account is already activated');
            return $this->redirectToRoute(
                'app_user_show',
                [
                    'id' => $user->getId()
                ]
            );
        }

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];


        $payload = [
            'user_id' => $user->getId()
        ];

        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        $sendMailService->send(
            'yasmine.souissi@esprit.tn',
            $user->getEmail(),
            'Welcome to GoalsLink',
            'register',
            compact('user', 'token')
        );
        $this->addFlash('success', 'Verification email sent');
        return $this->redirectToRoute(
            'app_user_show',
            [
                'id' => $user->getId()
            ]
        );
    }
    #[Route('/memberAuth/{token}', name: 'member_auth')]
    public function newUserAsMember(
        Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager,
        SendMailService $sendMailService, JWTService $jwt, MemberRepository $memberRepository,
        $token
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $member = $memberRepository->findOneBy(['id' => $token]);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $member->setIsActive(true);
            $member->setUserId($user->getId());
            $entityManager->persist($member);
            $entityManager->flush();
            $user->setEmail($member->getEmail());
            $user->setType('organazation');
            $user->setRoles(['ROLE_USER', 'ROLE_MEMBER']);
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // Create JWT Header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // Create JWT Payload
            $payload = [
                'user_id' => $user->getId()
            ];

            // Generate JWT
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            // send email to user
            $sendMailService->send(
                'yasmine.souissi@esprit.tn',
                $user->getEmail(),
                'Welcome to GoalsLink',
                'register',
                compact('user', 'token')
            );


            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'member' => $member
        ]);
    }

}