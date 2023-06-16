<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class ProductsFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($prod = 1; $prod <= 25; $prod++){
            $product = new Product();
            $product->setName($faker->text(5));
            $product->setDescription($faker->text());
            $product->setSlug($this->slugger->slug($product->getName())->lower());
            $product->setPrice($faker->numberBetween(900, 25000));
            $product->setStock($faker->numberBetween(0, 15));

            $category = $this->getReference('cat-' .rand(1, 12));
            $product->setCategories($category);

            $this->setReference('prod-' .$prod, $product);

            $manager->persist($product);

        }

        $manager->flush();
    }
}
