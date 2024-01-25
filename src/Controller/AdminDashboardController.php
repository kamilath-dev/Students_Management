<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            // Admin is not authenticated, redirect to the login page
            return $this->redirectToRoute('admin_login');
        }

        // Admin is authenticated, redirect to the dashboard
        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    public function dashboard(): Response
    {

         // Check if the admin is authenticated
         if (!$this->getUser()) {
            // Admin is not authenticated, redirect to the login page
            return $this->redirectToRoute('admin_login');
        }

        $etudiants = $this->getDoctrine()->getRepository(Etudiant::class)->findAll();

        return $this->render('admin_dashboard/index.html.twig', [
            'etudiants' => $etudiants,
        ]);
    }

    

    /**
     * @Route("/admin/etudiant/ajouter", name="admin_ajouter_etudiant", methods={"POST"})
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

        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * @Route("/admin/etudiant/modifier/{id}", name="admin_modifier_etudiant", methods={"POST"})
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

        return $this->redirectToRoute('admin_dashboard');
    }

    /**
    * @Route("/admin/etudiant/supprimer/{id}", name="admin_supprimer_etudiant", methods={"DELETE"})
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

        return $this->redirectToRoute('admin_dashboard');
    }

    /**
 * @Route("/admin/login", name="admin_login")
 */
public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
{
    // Récupère les erreurs de connexion, le cas échéant
    $error = $authenticationUtils->getLastAuthenticationError();

    // Récupère le dernier nom d'utilisateur (email) entré par l'admin
    $lastUsername = $authenticationUtils->getLastUsername();

    // Redirect to the dashboard if user is already authenticated
    if ($this->getUser()) {
        return $this->redirectToRoute('admin_dashboard');
    }

    return $this->render('admin_dashboard/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error,
    ]);
}


    /**
     * @Route("/admin/logout", name="admin_logout")
     */
    public function logout()
    {
        $this->get('security.token_storage')->setToken(null);
        $this->get('session')->invalidate();

        // Redirect to a route or URL after logout (example: back to login page)
        return $this->redirectToRoute('admin_login');
        // Cette méthode est vide car Symfony gère automatiquement la déconnexion
    }

    /**
     * @Route("/admin/profil", name="admin_profil")
     */
    public function profil(): Response
    {
         // Récupère l'admin actuellement connecté (l'admin authentifié)
        $admin = $this->getUser();

        // Check if the admin is authenticated
        if (!$admin) {
            // Redirect to the login page or handle the case when not authenticated
            // For example, you can return an error message or redirect to the login page
            return $this->redirectToRoute('admin_login');
    }

        // Affiche le profil de l'admin dans le template
        return $this->render('admin_dashboard/profil.html.twig', [
            'admin' => $admin,
        ]);
    }

    /**
     * @Route("/admin/profil/modifier", name="admin_modifier_profil", methods={"POST"})
     */
    public function modifierProfil(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $data = $request->request->all();
        $entityManager = $this->getDoctrine()->getManager();

        // Récupère l'admin actuellement connecté (l'admin authentifié)
        $admin = $this->getUser();

        // Met à jour les propriétés du profil de l'admin
        $admin->setUsername($data['username']);
        // ... (d'autres propriétés du profil de l'admin)

        // Vérifie si le mot de passe a été modifié
        if (!empty($data['password'])) {
            // Hash le nouveau mot de passe avant de le sauvegarder
            $hashedPassword = $passwordEncoder->encodePassword($admin, $data['password']);
            $admin->setPassword($hashedPassword);
        }

        // Sauvegarde les modifications dans la base de données
        $entityManager->flush();

        return $this->redirectToRoute('admin_profil');
    }


}
