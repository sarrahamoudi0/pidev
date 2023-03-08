<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\RequestType;
use App\Repository\ContactRepository;
use App\Form\ContactType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




class ContactController extends AbstractController
{

    #[Route('/contact/{id}', name: 'app_contact')]
    public function index($id, ContactRepository $repository, Request $request, ManagerRegistry $doctrine): Response
    {
        $Contacts = $repository->findBy(['Request' => $id]);

        $contact = new Contact();
        $contact->setSenderId(
            $this->getUser()->getId()
        );
        $contact->setRecieverId(
            $doctrine->getRepository(\App\Entity\Request::class)->find($id)->getRecieverId()
        );
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        $req = $doctrine->getRepository(\App\Entity\Request::class)->find($id);
        $contact->setSenderId($this->getUser()->getId());
        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData()->setRequest($req);
            $em = $doctrine->getManager();
            $em->persist($contact);
            $em->flush();
            $Contacts[] = $form->getData();
        }
        return $this->renderForm(
            'contact/index.html.twig',
            array('Contacts' => $Contacts, "formContact" => $form)
        );
    }
    #[Route('/contact/add/{id}', name: 'addContactForm')]
    public function addcontactForm($id, Request $request, ManagerRegistry $doctrine)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $contact->setRequest(
            $doctrine->getRepository(\App\Entity\Request::class)->find($id)
        );
        $form->handleRequest($request);
        $req = $doctrine->getRepository(\App\Entity\Request::class)->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData()->setRequest($req);
            $em = $doctrine->getManager();
            $em->persist($contact);
            $em->flush();
            return $this->redirectToRoute("app_contact", array("id" => $id));
        }
        return $this->redirectToRoute("app_contact", array("formContact" => $form, "id" => $id, "message" => $contact->getMessage()));

    }
    #[Route('/contact/update/{id}', name: 'update_contact')]
    public function updatecontactForm($id, ContactRepository $repository, Request $request, ManagerRegistry $doctrine)
    {
        $contact = $repository->find($id);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute("app_contact");
        }

        return $this->renderForm("contact/update.html.twig", array("formContact" => $form, "id" => $id, "message" => $contact->getMessage()));
    }
    #[Route('/contact/update/{id}/fl', name: 'updatefl_contact')]
    public function updatecontact($id, ContactRepository $repository)
    {
        $req = $repository->find($id);

        $req->setMessage('hiiiiiiiiii');

        $repository->add($req);
        return $this->redirectToRoute("app_contact");


    }
    #[Route('/contact/remove/{id}', name: 'remove_contact')]
    public function remove(ManagerRegistry $doctrine, $id, ContactRepository $repository)
    {
        $request = $repository->find($id);
        $em = $doctrine->getManager();

        $em->remove($request);
        $em->flush();
        return $this->redirectToRoute("app_contact", ['id' => $request->getRequest()->getId()]);
    }

}