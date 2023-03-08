<?php

namespace App\Controller;

use App\Entity\Member;
use App\Form\MemberType;
use App\Repository\MemberRepository;
use App\Repository\UserRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/member')]
class MemberController extends AbstractController
{
    #[Route('/', name: 'app_member_index', methods: ['GET'])]
    public function index(MemberRepository $memberRepository): Response
    {
        return $this->render('member/index.html.twig', [
            'members' => $memberRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_member_new', methods: ['GET', 'POST'])]
    public function new (
        Request $request, MemberRepository $memberRepository, SendMailService $sendMailService,
        UserRepository $userRepository,
        $id
    ): Response
    {
        $member = new Member();
        $user = $userRepository->find($id);
        $member->setOrganizationId($user);
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $memberRepository->add($member);
            // send email to user
            $sendMailService->send(
                'yasmine.souissi@esprit.tn',
                $form->getData()->getEmail() ? $form->getData()->getEmail() : 'test@gmail.com',
                'Welcome to GoalsLink',
                'member',
                compact('member')
            );

            return $this->redirectToRoute('app_member_organizationId', [
                'id' => $user->getId()

            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('member/new.html.twig', [
            'member' => $member,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_member_show', methods: ['GET'])]
    public function show(Member $member): Response
    {
        return $this->render('member/show.html.twig', [
            'member' => $member,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_member_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Member $member, MemberRepository $memberRepository): Response
    {
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $memberRepository->add($member);
            return $this->redirectToRoute(
                'app_member_organizationId',
                [
                    'id' => $member->getOrganizationId()->getId()
                ], Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('member/edit.html.twig', [
            'member' => $member,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_member_delete', methods: ['POST'])]
    public function delete(Request $request, Member $member, MemberRepository $memberRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $member->getId(), $request->request->get('_token'))) {
            $memberRepository->remove($member);
        }

        return $this->redirectToRoute(
            'app_member_organizationId',
            [
                'id' => $member->getOrganizationId()->getId()
            ], Response::HTTP_SEE_OTHER
        );

    }

    #[Route('/organization/members/{id}', name: 'app_member_organizationId', methods: ['GET', 'POST'])]
    public function getMemberByOrganization(Request $request, MemberRepository $memberRepository, $id): Response
    {
        $members = $memberRepository->findBy(['organizationId' => $id]);
        return $this->render('member/index.html.twig', [
            'members' => $members,
        ]);
    }

}