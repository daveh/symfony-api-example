<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use DateTime;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product;

        $product->setName("Product One");
        $product->setSize(100);
        $product->setPublishedOn(new DateTime("2025-07-25"));

        $manager->persist($product);

        $product = new Product;

        $product->setName("Product Two");
        $product->setSize(200);
        $product->setPublishedOn(new DateTime("2025-06-15"));
        $product->setIsAvailable(false);

        $manager->persist($product);

        $manager->flush();
    }
}
