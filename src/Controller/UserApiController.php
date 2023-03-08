<?php
namespace App\Controller;

use App\Service\JWTService;
use App\Service\SendMailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;


class UserApiController extends AbstractController
{

    /**
     * @Route("/api/register", name="api_register")
     */
    public function register(
        Request $request, UserPasswordEncoderInterface $passwordEncoder,
        SendMailService $sendMailService, JWTService $jwt
    )
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setEmail($request->query->get('email'));
        $user->setPassword($passwordEncoder->encodePassword($user, $request->query->get('password')));
        $user->setRoles(['ROLE_USER']);
        $user->setFirstName($request->query->get('firstName'));
        $user->setLastName($request->query->get('lastName'));
        $user->setAddress($request->query->get('address'));
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());
        $user->setType($request->query->get('type'));
        try {
            $entityManager->persist($user);
            $entityManager->flush();
            return new JsonResponse(['status' => 'User created!'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'User not created!'], Response::HTTP_BAD_REQUEST);
        }
    }
    /**
     * @Route("/api/login", name="api_login")
     */
    public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $email = $request->query->get('email');
        $password = $request->query->get('password');

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            return new JsonResponse(['status' => 'User not found!'], Response::HTTP_NOT_FOUND);
        }
        $isValid = $passwordEncoder->isPasswordValid($user, $password);
        if (!$isValid) {
            return new JsonResponse(['status' => 'Invalid password!'], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse(['status' => 'User logged in!'], Response::HTTP_OK);

    }

    /**
     * @Route("/api/user/edit", name="api_user_edit")
     */
    public function edit(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $request->query->get('id')]);
        if (!$user) {
            return new JsonResponse(['status' => 'User not found!'], Response::HTTP_NOT_FOUND);
        }
        $user->setEmail($request->query->get('email'));
        $user->setPassword($passwordEncoder->encodePassword($user, $request->query->get('password')));
        $user->setRoles(['ROLE_USER']);
        $user->setFirstName($request->query->get('firstName'));
        $user->setLastName($request->query->get('lastName'));
        $user->setAddress($request->query->get('address'));
        $user->setUpdatedAt(new \DateTimeImmutable());
        $user->setType($request->query->get('type'));
        try {
            $entityManager->persist($user);
            $entityManager->flush();
            return new JsonResponse(['status' => 'User updated!'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'User not updated!'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/user/delete", name="api_user_delete")
     */
    public function delete(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $request->query->get('id')]);
        if (!$user) {
            return new JsonResponse(['status' => 'User not found!'], Response::HTTP_NOT_FOUND);
        }
        try {
            $entityManager->remove($user);
            $entityManager->flush();
            return new JsonResponse(['status' => 'User deleted!'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'User not deleted!'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/user/get", name="api_user_get")
     */

    // public function get(Request $request)
    // {
    //     $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $request->query->get('id')]);
    //     if (!$user) {
    //         return new JsonResponse(['status' => 'User not found!'], Response::HTTP_NOT_FOUND);
    //     }
    //     return new JsonResponse(['status' => 'User found!', 'user' => $user], Response::HTTP_OK);
    // }

    /**
     * @Route("/api/user/getAll", name="api_user_getAll")
     */

    public function getAll()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        if (!$users) {
            return new JsonResponse(['status' => 'Users not found!'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['status' => 'Users found!', 'users' => $users], Response::HTTP_OK);
    }
}

// http://127.0.0.1:8000/api/register?email=yas@gmail.com&password=test&type=%27member%27&firstName=Test&lastName=Test&address=Test