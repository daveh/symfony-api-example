<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductController extends AbstractController
{
    #[Route('/api/products', methods: ["GET"])]
    public function index(EntityManagerInterface $em,
                          SerializerInterface $serializer): JsonResponse
    {
        $products = $em->getRepository(Product::class)->findAll();

        $json_content = $serializer->serialize($products, "json", [
            ObjectNormalizer::IGNORED_ATTRIBUTES => ["id"]
        ]);

        // return $this->json($products);

        return JsonResponse::fromJsonString($json_content);
    }

    #[Route("/api/products/{id}", methods: ["GET"])]
    public function show(Product $product): JsonResponse
    {
        return $this->json($product);
    }

    #[Route("/api/products", methods: ["POST"])]
    public function create(Request $request,
                           SerializerInterface $serializer,
                           EntityManagerInterface $em,
                           ValidatorInterface $validator): JsonResponse
    {
        $content = $request->getContent();

        $product = $serializer->deserialize($content, Product::class, "json");

        $errors = $validator->validate($product);
        
        if (count($errors) > 0) {

            $error_messages = [];

            foreach ($errors as $error) {
                $error_messages[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(["errors" => $error_messages], 422);
        }

        $em->persist($product);
        $em->flush();

        return $this->json($product, 201);
    }

    #[Route("/api/products/{id}", methods: ["PUT", "PATCH"])]
    public function update(Request $request,
                           Product $product,
                           SerializerInterface $serializer,
                           EntityManagerInterface $em): JsonResponse
    {
        $serializer->deserialize($request->getContent(),
                                 Product::class,
                                 "json",
                                ["object_to_populate" => $product]);

        $em->flush();

        return $this->json($product);
    }

    #[Route("/api/products/{id}", methods: ["DELETE"])]
    public function delete(EntityManagerInterface $em,
                           Product $product): JsonResponse
    {
        $em->remove($product);

        $em->flush();

        return $this->json(null, 204);
    }
}
