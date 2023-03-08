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

class MemberApiController extends AbstractController
{
   /**
    * @Route("/api/createMember", name="api_createMember")
    */

    public function createMember(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

    }
}