<?php
namespace App\Controller\admin\playlists;

use App\Entity\Playlist;
use App\Form\PlaylistsType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur de admin playlists
 *
 * @author squareface
 */

class AdminPlaylistsController extends AbstractController {

    const PAGE_PLAYLISTS = "admin/playlists/admin.playlists.html.twig";
    const PAGE_PLAYLIST = "pages/playlist.html.twig";
    const PLAYLISTS_EDIT_PAGE = "admin/playlists/admin.playlists.edit.html.twig";
    const PLAYLISTS_ADD_PAGE = "admin/playlists/admin.playlists.add.html.twig";
    
    /**
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    public function __construct(
            PlaylistRepository $playlistRepository,
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository)
            {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * @Route("admin/playlists", name="admin.playlists")
     * @return Response
     */
    public function index(): Response{
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("admin/playlists/tri/{champ}/{ordre}", name="admin.playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response{
        if ($champ === "name") {
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        } elseif ($champ === "count") {
            $playlists = $this->playlistRepository->findAllOrderByCount($ordre);
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
	
    /**
     * @Route("admin/playlists/recherche/{champ}/{table}", name="admin.playlists.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * @Route("admin/playlists/playlist/{id}", name="admin.playlists.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->playlistRepository->findAllForOnePlaylist($id);
        return $this->render(self::PAGE_PLAYLIST, [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }

    /**
     * @Route("/admin/playlists/suppr/{id}", name="admin.playlists.suppr")
     * @param Playlist $playlist
     * @return Response
     */
    public function suppr(Playlist $playlist): Response {
        $totalVideos = count($playlist->getFormations());

        if ($totalVideos === 0) {
            $this->playlistRepository->remove($playlist, true);
        }   else {
            $this->addFlash('error', 'Impossible de supprimer la playlist car celle-ci n\'est pas vide');
        }
        return $this->redirectToRoute('admin.playlists');
    }

        /**
        * @Route("/admin/playlists/edit/{id}", name="admin.playlists.edit")
        * @param Playlist $playlist
        * @param Request $request
        * @return Response
        */
        public function edit(Playlist $playlist, Request $request): Response{
            $formPlaylists = $this->createForm(PlaylistsType::class, $playlist);
            $formPlaylists->handleRequest($request);
            if($formPlaylists->isSubmitted() && $formPlaylists->isValid()) {
                $this->playlistRepository->add($playlist, true);
                return $this->redirectToRoute('admin.playlists');
            }
            return $this->render(self::PLAYLISTS_EDIT_PAGE, [
                'playlist' => $playlist,
                'formplaylists' => $formPlaylists->createView()
            ]);
        }

        /**
     * @Route("/admin/playlists/add/", name="admin.playlists.add")
     * @param Playlist $playlist
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response {
        $playlist = new Playlist();
        $formPlaylists = $this->createForm(PlaylistsType::class, $playlist);
        $formPlaylists->handleRequest($request);
        if($formPlaylists->isSubmitted() && $formPlaylists->isValid()) {
            $this->playlistRepository->add($playlist, true);
            return $this->redirectToRoute('admin.playlists');
        }
        return $this->render(self::PLAYLISTS_ADD_PAGE, [
            'playlist' => $playlist,
            'formplaylists' => $formPlaylists->createView()
        ]);
    }
    
}