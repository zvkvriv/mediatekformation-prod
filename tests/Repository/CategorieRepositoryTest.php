<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategorieRepositoryTest extends KernelTestCase
{
    public function recupRepository(): CategorieRepository{
        self::bootKernel();
        $repository = self::getContainer()->get(CategorieRepository::class);
        return $repository;
    }


    public function testNbCategories() {
        $repository = $this->recupRepository();
        $nbCategories = $repository->count([]);
        $this->assertEquals(10, $nbCategories);
    }

    public function newCategorie(): Categorie {
        $categorie = (new Categorie())
                   ->setName("Nom de la catégorie");
        return $categorie;
    }

    public function testAddCategorie() {
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $nbCategories = $repository->count([]);
        $repository->add($categorie, true);
        $this->assertEquals($nbCategories + 1, $repository->count([]), "Erreur lors de l'ajout de la catégorie");
    }

    public function testRemoveCategorie() {
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $repository->add($categorie, true);
        $nbCategories = $repository->count([]);
        $repository->remove($categorie, true);
        $this->assertEquals($nbCategories - 1, $repository->count([]), "Erreur lors de la suppression de la catégorie");
    }

    public function testfindAllForOnePlaylist() {
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $repository->add($categorie, true);
        $categories = $repository->findAllForOnePlaylist(14);
        $nbCategories = count($categories);
        $this->assertEquals(1, $nbCategories);
        $this->assertEquals("SQL", $categories[0]->getName());
    }

    public function findAllOrderBy() {
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $repository->add($categorie, true);
        $categories = $repository->findAllOrderBy("name", "DESC");
        $nbCategories = count($categories);
        $this->assertEquals(1, $nbCategories);
        $this->assertEquals("UML", $categories[0]->getName());
    }
}
