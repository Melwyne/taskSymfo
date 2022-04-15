<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use App\Repository\TypeRepository;
use \Symfony\Component\HttpFoundation\Request;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;

class taskController extends AbstractController
{
    #[Route('/newTask', name: 'newTask', methods: ['GET', 'POST'])]
    public function newTask (TaskRepository $taskRepository, Request $request){

        $task = new Task();
        $taskForm = $this->createForm(TaskFormType::class, $task);
        $taskForm->handleRequest($request);
        if ($taskForm->isSubmitted() && $taskForm->isValid()){
            $task->setActive(true);
            $taskRepository->add($task);
            return $this->redirectToRoute('taskList');
        }
        return $this->render('Task/newTask.html.twig', [
            'taskForm' => $taskForm->createView()
        ]);
    }

    #[Route('/taskList', name: 'taskList', methods: ['GET', 'POST'])]
    public function taskList (TaskRepository $taskRepository, TypeRepository $typeRepository){
        $task = $taskRepository->findAll();
        $types = $typeRepository->findBy([], ['name' => 'ASC']);

        return $this->render('Task/taskList.html.twig', [
            'tasks' => $task,
            'types' => $types
        ]);
    }

    #[Route('/searchTask', name: 'searchTask', methods: ['GET', 'POST'])]
    public function searchTask (TaskRepository $taskRepository, Request $request, TypeRepository $typeRepository){

        $title = $request->query->get('title');
        $taskActive = $request->query->get('taskActive');
        $type = $request->query->get('typeName');
        $dateStart = $request->query->get('dateStart');
        $dateEnd = $request->query->get('dateEnd');

        //checkbox renvoie 'on' ou 'null' donc si $taskActive = 'on' ALORS
        if ($taskActive == 'on'){
            $taskActive = true;
        } else{
            $taskActive = false;
        }
        //Je récupère ma fonction que j'ai créée et je lui mets en paramètres $taskActive et $title
        $tasks = $taskRepository->searchTaskByTitle($title, $taskActive, $type, $dateStart, $dateEnd);
        //Je récupère tous mes types et les range par nom croissant
        $types = $typeRepository->findBy([], ['name' => 'ASC']);

        return $this->render('Task/taskList.html.twig', [
            'tasks' => $tasks,
            'types' => $types
        ]);
    }


    #[Route('/admin/updateTask/{id}', name: 'updateTask', methods: ['GET', 'POST'])]
    public function updateTask(TaskRepository $taskRepository, $id, Request $request){
        $task = $taskRepository->findOneBy(['id' => $id]);
        $taskForm = $this->createForm(TaskFormType::class, $task);
        $taskForm->handleRequest($request);
        if ($taskForm->isSubmitted() && $taskForm->isValid()){
            $taskRepository->add($task);
            return $this->redirectToRoute('taskList');
        }
        return $this->render('Task/updateTask.html.twig', [
            'updateTask' => $taskForm->createView()
        ]);
    }
}