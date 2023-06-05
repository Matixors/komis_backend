<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\Type\ProductType;
use App\Form\Type\UpdateProductType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractFOSRestController
{
    public function postProduct(Request $request, EntityManagerInterface $entityManager)
    {
        $product = new Product();

        $data = json_decode($request->getContent(), true);
        $createdAt = new \DateTime();
        $data['createdAt'] = $createdAt->format('Y-m-d H:i:s');

        $form = $this->createForm(ProductType::class, $product);

        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->handleView($this->view(['status' => 'ok, entity created'], Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }

    public function getAllCars(EntityManagerInterface $entityManager)
    {
        $repo = $entityManager->getRepository(Product::class);
        $cars = $repo->findAll();
        return $this->handleView($this->view($cars, Response::HTTP_ACCEPTED));
    }

    public function updateCar(Request $request,string $register, EntityManagerInterface $entityManager)
    {
        $repo = $entityManager->getRepository(Product::class);
        $product = $repo->findOneBy(['registerNumber'=>$register]);

        if (!$product)
        {
            return $this->handleView($this->view(['status'=>'not found', Response::HTTP_NOT_FOUND]));
        }

        $data = json_decode($request->getContent(), true);
        $form=$this->createForm(UpdateProductType::class, $product);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->handleView($this->view(['status' => 'ok, entity updated'], Response::HTTP_ACCEPTED));
        }
        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }

    public function deleteCar(string $register, EntityManagerInterface $entityManager)
    {
        $repo = $entityManager->getRepository(Product::class);
        $product = $repo->findOneBy(['registerNumber'=>$register]);

        if (!$product)
        {
            return $this->handleView($this->view(['status'=>'not found', Response::HTTP_NOT_FOUND]));
        }

        $entityManager->remove($product);
        $entityManager->flush();
        return $this->handleView($this->view(['status'=>'Entity successfully deleted'], Response::HTTP_OK));
    }
}