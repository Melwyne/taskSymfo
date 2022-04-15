<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypesType;
use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\HttpFoundation\Request;

class TypeController extends AbstractController
{
    #[Route('/type', name: 'type', methods: ['GET', 'POST'])]
    public function type(Request $request, TypeRepository $typeRepository){
        $type = new Type();
        $form = $this->createForm(TypesType::class, $type);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $typeRepository->add($type);
        }
        $types = $typeRepository->findBy([], ['name' => 'ASC']);
        return $this->render('type/types.html.twig', [
            'typeForm' => $form->createView(),
            'types' => $types
        ]);
    }

    #[Route('/deleteType/{id}', name: 'deleteType', methods: ['GET', 'POST'])]
    public function deleteType(TypeRepository $typeRepository, $id){
        $type = $typeRepository->findOneBy(['id' => $id]);
        $typeRepository->remove($type);
        return $this->redirectToRoute('type');
    }

}
