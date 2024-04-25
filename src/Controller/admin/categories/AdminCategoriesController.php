<?php

namespace App\Controller\admin\categories;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controleur de admin categories
 *
 * @author squareface
 */

class AdminCategoriesController extends AbstractController
{

    const CATEGORIES_PAGE = "admin/categories/admin.categories.html.twig";

    /**
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    /**
     * @Route("admin/categories", name="admin.categories")
     * @return Response
     */
    public function index(): Response{
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CATEGORIES_PAGE, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("admin/categories/tri/{champ}/{ordre}/{table}", name="admin.categories.sort")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sort($champ, $ordre, $table=""): Response{
        $categories = $this->categorieRepository->findAllOrderBy($champ, $ordre, $table);
        $formations = $this->formationRepository->findAll();
        return $this->render(self::CATEGORIES_PAGE, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/categories/suppr/{id}", name="admin.categories.suppr")
     * @param Categorie $categorie
     * @return Response
     */
    public function suppr(Categorie $categorie): Response {
        $totalVideos = count($categorie->getFormations());

        if ($totalVideos === 0) {
            $this->categorieRepository->remove($categorie, true);
        }   else {
            $this->addFlash('error', 'Impossible de supprimer la catégorie car elle est assignée à une formation');
        }
        return $this->redirectToRoute('admin.categories');
    }

    /**
     * @Route("/admin/categories/add", name="admin.categories.add")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response{
        $nomCategorie = $request->get("name");
        $categorieCheck = $this->categorieRepository->findOneBy(['name' => $nomCategorie]);

        if ($categorieCheck) {
            $this->addFlash('error', 'Une catégorie avec ce nom existe déjà.');
            return $this->redirectToRoute('admin.categories');
        }

        $categorie = new Categorie();
        $categorie->setName($nomCategorie);
        $this->categorieRepository->add($categorie, true);
        return $this->redirectToRoute('admin.categories');
    }
}
