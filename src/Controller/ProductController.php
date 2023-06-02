<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\Type\ProductType;
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
}