<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    private $counter = 1;

    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Informatique', null, $manager);

        $this->createCategory('Ordinateur de bureau', $parent, $manager);
        $this->createCategory('Ordinateur portable', $parent, $manager);
        $this->createCategory('Ecran', $parent, $manager);
        $this->createCategory('Clavier', $parent, $manager);
        $this->createCategory('Sourie sans fil', $parent, $manager);

        $parent = $this->createCategory('Hi-Fi', null, $manager);

        $this->createCategory('Platine vinyle', $parent, $manager);
        $this->createCategory('Chaine Hi-Fi', $parent, $manager);
        $this->createCategory('Platine CD', $parent, $manager);
        $this->createCategory('Enceinte Hi-Fi', $parent, $manager);
        $this->createCategory('Ampli Hi-Fi', $parent, $manager);

        $manager->flush();
    }

    public function createCategory(string $name, Category $parent = null, ObjectManager $manager)
    {
        $category = new Category();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $manager->persist($category);

        $this->addReference('cat-' .$this->counter, $category);
        $this->counter++;

        return $category;
    }
}
