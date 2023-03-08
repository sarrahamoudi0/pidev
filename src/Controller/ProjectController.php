<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;

use App\Repository\ProjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProjectController extends AbstractController


{
    #[Route('/projects', name: 'app_all_project')]
    public function index(ProjectRepository $repository): Response
    {
        $projects=$repository->findAll();
        return $this->render('project/index.html.twig',array('projects'=>$projects)
        );
    }

    #[Route('/userProjects', name: 'app_project')]
    public function userProjects(ProjectRepository $repository): Response
    {
        $projects=$repository->findBy(['owner_id'=>$this->getUser()->getId()]);
        return $this->render('project/index.html.twig',array('projects'=>$projects)
        );
    }

    #[Route('/project/add', name: 'addProjectForm')]
    public function addProjectForm(Request  $request,ManagerRegistry $doctrine,SluggerInterface $slugger)
    {
        $project= new  project();
        $form= $this->createForm(ProjectType::class,$project);
        $form->handleRequest($request) ;
        if($form->isSubmitted()&& $form->isValid()){
            $photo = $form->get('image')->getData();



            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('project_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $project->setImage($newFilename);
            }
            $project->setOwnerId($this->getUser()->getId());
            $em= $doctrine->getManager();
            $em->persist($project);
            $em->flush();
            return  $this->redirectToRoute("app_project");
        }
        return $this->renderForm ("project/add.html.twig",array("formProject"=>$form));
    }
    #[Route('/project/update/{id}', name: 'update_project')]
    public function updateprojectForm($id,ProjectRepository $repository,Request  $request,ManagerRegistry $doctrine)
    {
        $project= $repository->find($id);
        $form= $this->createForm(projectType::class,$project);
        $form->handleRequest($request) ;
        if($form->isSubmitted()){
            $em= $doctrine->getManager();
            $em->flush();
            return  $this->redirectToRoute("app_project");
        }
        return $this->renderForm("project/update.html.twig",array("formProject"=>$form,"id"=>$id));
    }
    #[Route('/project/remove/{id}', name: 'remove_project')]
    public function remove(ManagerRegistry $doctrine,$id,ProjectRepository $repository)
    {
        $project= $repository->find($id);
        $em= $doctrine->getManager();

        $em->remove($project);
        $em->flush();
        return $this->redirectToRoute("app_project");
    }
    #[Route('/project/{id}', name: 'showproject')]
    public function showProject($id,ProjectRepository $repository): Response
    {
        $project=$repository->find($id);
        return $this->render('project/show.html.twig',array('project'=>$project)
        );
    }

}
