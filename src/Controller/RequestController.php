<?php

namespace App\Controller;
use App\Form\RequestType;

use App\Entity\Request;

use App\Repository\RequestRepository;
use Doctrine\Persistence\ManagerRegistry;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




class RequestController extends AbstractController
{
   // #[Route('/request', name: 'app_request')]
  //  public function index(): Response
   // {
    //    return $this->render('request/index.html.twig', [
   //         'controller_name' => 'RequestController',
  //      ]);
   // }

    #[Route('/request', name: 'app_request')]
    public function index(RequestRepository $repository): Response
    {
        $Requests=$repository->findAll();
        return $this->render('request/index.html.twig',array('Requests'=>$Requests)
        );
    }
    #[Route('/request/add/{id}', name: 'addRequestForm')]
    public function addrequestForm(\Symfony\Component\HttpFoundation\Request  $request ,ManagerRegistry $doctrine,
    $id)
    {
        $req= new  Request();
        $form= $this->createForm(RequestType::class,$req);
        $form->handleRequest($request) ;
        $req->setSenderId($this->getUser()->getId());
        $req->setProjectId($id);
        $req->setRecieverId($doctrine->getRepository(\App\Entity\Project::class)->find($id)->getOwnerId());
        if($form->isSubmitted()&& $form->isValid()){
            $em= $doctrine->getManager();
            $em->persist($req);
            $em->flush();

            return  $this->redirectToRoute("app_request");
        }
        return $this->renderForm ("request/add.html.twig",array("formRequest"=>$form));
    }

    #[Route('/request/update/{id}/reject', name: 'reject_request')]
    public function rejectrequest($id,RequestRepository $repository)
    {
        $req= $repository->find($id);

        $req->setStatus("DECLINED");

        $repository->add($req);
        return  $this->redirectToRoute("app_request");


    }
    #[Route('/request/update/{id}/approve', name: 'approve_request')]
    public function approverequest($id,RequestRepository $repository)
    {
        $req= $repository->find($id);

        $req->setStatus("APPROVED");

        $repository->add($req);
        return  $this->redirectToRoute("app_request");


    }
    #[Route('/request/remove/{id}', name: 'remove_request')]
    public function remove(ManagerRegistry $doctrine,$id,RequestRepository $repository)
    {
        $request= $repository->find($id);
        $em= $doctrine->getManager();

        $em->remove($request);
        $em->flush();
        return $this->redirectToRoute("app_request");
    }
}
