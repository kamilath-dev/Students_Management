<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtudiantController extends AbstractController
{
    #[Route('/etudiant', name: 'app_etudiant')]
    public function index(): Response
    {
        return $this->render('etudiant/index.html.twig', [
            'controller_name' => 'EtudiantController',
        ]);
    }
    /**
     * @Route("/etudiant/ajouter", name="ajouter_etudiant", methods={"POST"})
     */
    public function ajouterEtudiant(Request $request): Response
    {
        $data = $request->request->all();

        // Crée un nouvel objet Etudiant à partir des données envoyées depuis le formulaire
        $etudiant = new Etudiant();
        $etudiant->setNom($data['nom']);
        $etudiant->setPrenom($data['prenom']);
        $etudiant->setEmail($data['email']);
        // ... (d'autres propriétés de l'étudiant)

        // Sauvegarde l'objet Etudiant dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($etudiant);
        $entityManager->flush();

        return $this->redirectToRoute('liste_etudiants');
    }

     /**
     * @Route("/etudiants", name="liste_etudiants", methods={"GET"})
     */
    public function listeEtudiants(): Response
    {
        $etudiants = $this->getDoctrine()->getRepository(Etudiant::class)->findAll();

        return $this->render('etudiant/liste.html.twig', [
            'etudiants' => $etudiants,
        ]);
    }

    /**
     * @Route("/etudiant/modifier/{id}", name="modifier_etudiant", methods={"POST"})
     */
    public function modifierEtudiant(Request $request, int $id): Response
    {
        $data = $request->request->all();
        $entityManager = $this->getDoctrine()->getManager();

        // Récupère l'étudiant à modifier depuis la base de données
        $etudiant = $entityManager->getRepository(Etudiant::class)->find($id);

        if (!$etudiant) {
            throw $this->createNotFoundException('Aucun étudiant trouvé pour l\'id '.$id);
        }

        // Met à jour les propriétés de l'étudiant
        $etudiant->setNom($data['nom']);
        $etudiant->setPrenom($data['prenom']);
        $etudiant->setEmail($data['email']);
        // ... (d'autres propriétés de l'étudiant)

        // Sauvegarde les modifications dans la base de données
        $entityManager->flush();

        return $this->redirectToRoute('liste_etudiants');
    }

    
    /**
     * @Route("/etudiant/supprimer/{id}", name="supprimer_etudiant", methods={"DELETE"})
     */
    public function supprimerEtudiant(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Récupère l'étudiant à supprimer depuis la base de données
        $etudiant = $entityManager->getRepository(Etudiant::class)->find($id);

        if (!$etudiant) {
            throw $this->createNotFoundException('Aucun étudiant trouvé pour l\'id '.$id);
        }

        // Supprime l'étudiant de la base de données
        $entityManager->remove($etudiant);
        $entityManager->flush();

        return $this->redirectToRoute('liste_etudiants');
    }
}
