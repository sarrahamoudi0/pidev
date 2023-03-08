<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Project;
use App\Form\TaskType;

use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'app_task')]
    public function index(TaskRepository $repository): Response
    {
        $tasks = $repository->findAll();
        return $this->render(
            'task/index.html.twig',
            array('tasks' => $tasks)
        );
    }
    #[Route('/task/add/{id}', name: 'addTaskForm')]
    public function addTaskForm($id, Request $request, ManagerRegistry $doctrine, Security $security)
    {
        $task = new Task();
        $form = $this->createForm(
            TaskType::class,
            $task

        );
        $form->handleRequest($request);
        $project = $doctrine->getRepository(Project::class)->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData()->setProject($project);
            $em = $doctrine->getManager();
            $em->persist($task);
            $em->flush();
            return $this->redirectToRoute(
                "show_tasks_by_project",
                array("id" => $id)
            );
        }
        return $this->renderForm(
            "task/add.html.twig",
            array(
                "formTask" => $form,
                'security' => $security
            )
        );
    }
    #[Route('/task/update/{id}', name: 'update_task')]
    public function updateTaskForm($id, TaskRepository $repository, Request $request, ManagerRegistry $doctrine)
    {
        $task = $repository->find($id);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute("app_task");
        }
        return $this->renderForm("task/update.html.twig", array("formTask" => $form, "id" => $id));
    }
    #[Route('/task/remove/{id}', name: 'remove_task')]
    public function remove(ManagerRegistry $doctrine, $id, TaskRepository $repository)
    {
        $task = $repository->find($id);
        $em = $doctrine->getManager();

        $em->remove($task);
        $em->flush();
        return $this->redirectToRoute("app_task");
    }
    #[Route('/task/project/{id}', name: 'show_tasks_by_project')]
    public function showTasksByProjectId($id, TaskRepository $repository, ProjectRepository $projectRepository): Response
    {
        $tasks = $repository->findBy(array("Project" => $id));
        return $this->render(
            'task/index.html.twig',
            array('tasks' => $tasks)
        );
    }
}