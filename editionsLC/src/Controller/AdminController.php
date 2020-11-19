<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Auteur;
use App\Entity\Salon;
use App\Entity\Livre;
use App\Entity\Vente;
use App\Entity\ServicePresse;
use App\Entity\BonDeDepot;
use App\Entity\Participation;
use App\Form\AjouterAuteurType;
use App\Form\ModifierAuteurType;
use App\Form\AjouterSalonType;
use App\Form\ModifierSalonType;
use App\Form\AjouterLivreType;
use App\Form\ModifierLivreType;
use App\Form\AjouterVenteType;
use App\Form\ModifierVenteType;
use App\Form\AjouterBonType;
use App\Form\ModifierBonType;
use App\Form\AjouterServiceType;
use App\Form\ModifierServiceType;
use App\Form\ParAuteurType;
use App\Form\ParLivreType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        return $this->render('admin/admin.html.twig');
    }

    /**
     * @Route("/admin-ajouter-auteur", name="admin-ajouter-auteur")
     */
    public function adminAjouterAuteur(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $auteur = new Auteur();
        $form = $this->createForm(AjouterAuteurType::class, $auteur);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $auteurs = $this->getAuteurs();
                foreach ($auteurs as $unAuteur) {
                    if ($unAuteur->getNom() == $form['nom']->getData() && $unAuteur->getPrenom() == $form['prenom']->getData()) {
                        unset($auteur);
                        unset($form);
                        $auteur = new Auteur();
                        $form = $this->createForm(AjouterAuteurType::class, $auteur);
                        return $this->render('admin/adminAjouterAuteur.html.twig', array('form' => $form->createView(), 'error' => 1));
                    }
                }
                $auteur->setNom($form['nom']->getData());
                $auteur->setPrenom($form['prenom']->getData());
                $auteur->setVille($form['ville']->getData());
                $pseudo = strtoupper(substr($form['nom']->getData(), 0, 1)).strtolower($form['prenom']->getData());
                $auteur->setPseudo($pseudo);
                $mdp = strtolower(substr($form['prenom']->getData(), 0, 1)).strtoupper(substr($form['nom']->getData(), 0, 1)).strval(rand(1010, 9999));
                $auteur->setMdp($mdp);
                $em = $this->getDoctrine()->getManager();
                $em->persist($auteur);
                $em->flush();
                unset($auteur);
                unset($form);
                $auteur = new Auteur();
                $form = $this->createForm(AjouterAuteurType::class, $auteur);
                return $this->render('admin/adminAjouterAuteur.html.twig', array('form' => $form->createView(), 'error' => -1));
            }
        }
        return $this->render('admin/adminAjouterAuteur.html.twig', array('form' => $form->createView(), 'error' => 0));
    }

    /**
     * @Route("/admin-auteurs", name="admin-auteurs")
     */
    public function adminAuteurs(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $auteurs = $this->getAuteurs();
        if ($session->get('auteurSupprime') == 1) {
            $session->set('auteurSupprime', 0);
            return $this->render('admin/adminAuteurs.html.twig', array('auteurs'=>$auteurs, 'error' => -1));
        }
        return $this->render('admin/adminAuteurs.html.twig', array('auteurs'=>$auteurs, 'error' => 0));
    }

    /**
     * @Route("/admin-modification-auteur/{id}", name="admin-modification-auteur")
     */
    public function adminModificationAuteur($id, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $session->set('idModifierAuteur', $id);
        return $this->redirectToRoute('admin-modifier-auteur');
    }

    /**
     * @Route("/admin-modifier-auteur", name="admin-modifier-auteur")
     */
    public function adminModifierAuteur(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $auteur = $this->getDoctrine()->getRepository(\App\Entity\Auteur::class)->findOneBy(['id' => $session->get('idModifierAuteur')]);
        $form = $this->createForm(ModifierAuteurType::class, $auteur);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $auteur->setNom($form['nom']->getData());
                $auteur->setPrenom($form['prenom']->getData());
                $auteur->setVille($form['ville']->getData());
                $pseudo = strtoupper(substr($form['nom']->getData(), 0, 1)).strtolower($form['prenom']->getData());
                $auteur->setPseudo($pseudo);
                $auteur->setMdp($form['mdp']->getData());
                $em = $this->getDoctrine()->getManager();
                $em->persist($auteur);
                $em->flush();
                unset($auteur);
                unset($form);
                $auteur = $this->getDoctrine()->getRepository(\App\Entity\Auteur::class)->findOneBy(['id' => $session->get('idModifierAuteur')]);
                $form = $this->createForm(ModifierAuteurType::class, $auteur);
                return $this->render('admin/adminModifierAuteur.html.twig', array('form' => $form->createView(), 'error' => -1, 'auteur' => $auteur));
            }
        }
        return $this->render('admin/adminModifierAuteur.html.twig', array('form' => $form->createView(), 'error' => 0, 'auteur' => $auteur));
    }

    /**
     * @Route("/admin-suppression-auteur/{id}/{request}", name="admin-suppression-auteur")
     */
    public function adminSuppressionAuteur($id, $request, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        if ($request == 0) {
            $session->set('idSupprimerAuteur', $id);
            return $this->redirectToRoute('admin-supprimer-auteur');
        }
        if ($request == 1) {
            $em = $this->getDoctrine()->getManager();
            $auteur = $this->getDoctrine()->getRepository(\App\Entity\Auteur::class)->findOneBy(['id' => $session->get('idSupprimerAuteur')]);
            $livres = $this->getDoctrine()->getRepository(\App\Entity\Livre::class)->findBy(['auteur' => $auteur]);
            foreach ($livres as $unLivre) {
                $ventes = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findBy(['livre' => $unLivre]);
                foreach ($ventes as $uneVente) {
                    $em->remove($uneVente);
                }
                $bons = $this->getDoctrine()->getRepository(\App\Entity\BonDeDepot::class)->findBy(['livre' => $unLivre]);
                foreach ($bons as $unBon) {
                    $em->remove($unBon);
                }
                $services = $this->getDoctrine()->getRepository(\App\Entity\ServicePresse::class)->findBy(['livre' => $unLivre]);
                foreach ($services as $unService) {
                    $em->remove($unService);
                }
                $em->remove($unLivre);
            }
            $participations = $this->getDoctrine()->getRepository(\App\Entity\Participation::class)->findBy(['auteur' => $auteur]);
            foreach($participations as $uneParticipation) {
                $em->remove($uneParticipation);
            }
            $em->remove($auteur);
            $em->flush();
            $session->set('auteurSupprime', 1);
            return $this->redirectToRoute('admin-auteurs');
        }
    }

    /**
     * @Route("/admin-supprimer-auteur", name="admin-supprimer-auteur")
     */
    public function adminSupprimerAuteur(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $auteur = $this->getDoctrine()->getRepository(\App\Entity\Auteur::class)->findOneBy(['id' => $session->get('idSupprimerAuteur')]);
        return $this->render('admin/adminSupprimerAuteur.html.twig', array('auteur' => $auteur));
    }

    /**
     * @Route("/admin-get-id-auteur/{id}/{request}", name="admin-get-id-auteur")
     */
    public function adminGetIdAuteur($id, $request, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $session->set('idAuteur', $id) ;
        if ($request == 0)
            return $this->redirectToRoute('admin-livres-auteur');
        if ($request == 1)
            return $this->redirectToRoute('admin-ventes-auteur');
    }

    /**
     * @Route("/admin-livres-auteur", name="admin-livres-auteur")
     */
    public function adminLivresAuteur(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $livres = $this->getDoctrine()->getRepository(\App\Entity\Livre::class)->findBy(['auteur' => $session->get('idAuteur')]);
        return $this->render('admin/adminLivres.html.twig',array('livres'=>$livres, 'error' => 0));
    }

    /**
     * @Route("/admin-ventes-auteur", name="admin-ventes-auteur")
     */
    public function adminVentesAuteur(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $ventes = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findByAuteur($session->get('idAuteur'));
        return $this->render('admin/adminVentes.html.twig',array('ventes'=>$ventes, 'error' => 0));
    }

    /**
     * @Route("/admin-ajouter-livre", name="admin-ajouter-livre")
     */
    public function adminAjouterLivre(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $livre = new Livre();
        $form = $this->createForm(AjouterLivreType::class, $livre);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $livres = $this->getLivres();
                foreach ($livres as $unLivre) {
                    if ($unLivre->getTitre() == $form['titre']->getData()) {
                        unset($livre);
                        unset($form);
                        $livre = new Livre();
                        $form = $this->createForm(AjouterLivreType::class, $livre);
                        return $this->render('admin/adminAjouterLivre.html.twig', array('form' => $form->createView(), 'error' => 1));
                    }
                }
                $livre->setTitre($form['titre']->getData());
                $livre->setAuteur($form['auteur']->getData());
                $livre->setStock($form['stock']->getData());
                $em = $this->getDoctrine()->getManager();
                $em->persist($livre);
                $em->flush();
                unset($livre);
                unset($form);
                $livre = new Livre();
                $form = $this->createForm(AjouterLivreType::class, $livre);
                return $this->render('admin/adminAjouterLivre.html.twig', array('form' => $form->createView(), 'error' => -1));
            }
        }
        return $this->render('admin/adminAjouterLivre.html.twig', array('form' => $form->createView(), 'error' => 0));
    }

    /**
     * @Route("/admin-livres", name="admin-livres")
     */
    public function adminLivres(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $livres = $this->getLivres();
        if ($session->get('livreSupprime') == 1) {
            $session->set('livreSupprime', 0);
            return $this->render('admin/adminLivres.html.twig', array('livres'=>$livres, 'error' => -1));
        }
        return $this->render('admin/adminLivres.html.twig',array('livres'=>$livres, 'error' => 0));
    }

    /**
     * @Route("/admin-modification-livre/{id}", name="admin-modification-livre")
     */
    public function adminModificationLivre($id, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $session->set('idModifierLivre', $id);
        return $this->redirectToRoute('admin-modifier-livre');
    }

    /**
     * @Route("/admin-modifier-livre", name="admin-modifier-livre")
     */
    public function adminModifierLivre(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $livre = $this->getDoctrine()->getRepository(\App\Entity\Livre::class)->findOneBy(['id' => $session->get('idModifierLivre')]);
        $form = $this->createForm(ModifierLivreType::class, $livre);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $livre->setTitre($form['titre']->getData());
                $livre->setAuteur($form['auteur']->getData());
                $livre->setStock($form['stock']->getData());
                $em = $this->getDoctrine()->getManager();
                $em->persist($livre);
                $em->flush();
                unset($livre);
                unset($form);
                $livre = $this->getDoctrine()->getRepository(\App\Entity\Livre::class)->findOneBy(['id' => $session->get('idModifierLivre')]);
                $form = $this->createForm(ModifierLivreType::class, $livre);
                return $this->render('admin/adminModifierLivre.html.twig', array('form' => $form->createView(), 'error' => -1));
            }
        }
        return $this->render('admin/adminModifierLivre.html.twig', array('form' => $form->createView(), 'error' => 0));
    }

    /**
     * @Route("/admin-suppression-livre/{id}/{request}", name="admin-suppression-livre")
     */
    public function adminSuppressionLivre($id, $request, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        if ($request == 0) {
            $session->set('idSupprimerLivre', $id);
            return $this->redirectToRoute('admin-supprimer-livre');
        }
        if ($request == 1) {
            $em = $this->getDoctrine()->getManager();
            $livre = $this->getDoctrine()->getRepository(\App\Entity\Livre::class)->findOneBy(['id' => $session->get('idSupprimerLivre')]);
            $ventes = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findBy(['livre' => $livre]);
            foreach ($ventes as $uneVente) {
                $em->remove($uneVente);
            }
            $bons = $this->getDoctrine()->getRepository(\App\Entity\BonDeDepot::class)->findBy(['livre' => $livre]);
            foreach ($bons as $unBon) {
                $em->remove($unBon);
            }
            $services = $this->getDoctrine()->getRepository(\App\Entity\ServicePresse::class)->findBy(['livre' => $livre]);
            foreach ($services as $unService) {
                $em->remove($unService);
            }
            $participations = $this->getDoctrine()->getRepository(\App\Entity\Participation::class)->findBy(['livre' => $livre]);
            foreach($participations as $uneParticipation) {
                $em->remove($uneParticipation);
            }
            $em->remove($livre);
            $em->flush();
            $session->set('livreSupprime', 1);
            return $this->redirectToRoute('admin-livres');
        }
    }

    /**
     * @Route("/admin-supprimer-livre", name="admin-supprimer-livre")
     */
    public function adminSupprimerLivre(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $livre = $this->getDoctrine()->getRepository(\App\Entity\Livre::class)->findOneBy(['id' => $session->get('idSupprimerLivre')]);
        return $this->render('admin/adminSupprimerLivre.html.twig', array('livre' => $livre));
    }

    /**
     * @Route("/admin-livres-par-auteur", name="admin-livres-par-auteur")
     */
    public function adminLivresParAuteur(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $auteur = [];
        $form = $this->createForm(ParAuteurType::class, $auteur);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $livres = $this->getDoctrine()->getRepository(\App\Entity\Livre::class)->findBy(['auteur' => $form['auteur']->getData()]);
                return $this->render('admin/adminLivres.html.twig',array('livres'=>$livres, 'error' => 0));
            }
        }
        return $this->render('admin/adminLivresParAuteur.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/admin-get-id-livre/{id}", name="admin-get-id-livre")
     */
    public function adminGetIdLivre($id, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $session->set('idLivre', $id) ;
        return $this->redirectToRoute('admin-ventes-livre');
    }

    /**
     * @Route("/admin-ventes-livre", name="admin-ventes-livre")
     */
    public function adminVentesLivre(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $ventes = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findBy(['livre' => $session->get('idLivre')]);
        return $this->render('admin/adminVentes.html.twig',array('ventes'=>$ventes, 'error' => 0));
    }

    /**
     * @Route("/admin-ajouter-vente", name="admin-ajouter-vente")
     */
    public function adminAjouterVente(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $vente = new Vente();
        $form = $this->createForm(AjouterVenteType::class, $vente);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $ventes = $this->getVentes();
                foreach ($ventes as $uneVente) {
                    if ($uneVente->getDate() == $form['date']->getData() && $uneVente->getLivre()->getTitre() == $form['livre']->getData()->getTitre() && $uneVente->getSource() == $form['source']->getData()) {
                        unset($vente);
                        unset($form);
                        $vente = new Vente();
                        $form = $this->createForm(AjouterVenteType::class, $vente);
                        return $this->render('admin/adminAjouterVente.html.twig', array('form' => $form->createView(), 'error' => 1));
                    }
                }
                $vente->setDate($form['date']->getData());
                $vente->setSource($form['source']->getData());
                $vente->setPrix($form['prix']->getData());
                $vente->setNbVentes($form['nbVentes']->getData());
                $vente->setLivre($form['livre']->getData());
                $vente->getLivre()->setStock($vente->getLivre()->getStock() - $vente->getNbVentes());
                $em = $this->getDoctrine()->getManager();
                $em->persist($vente);
                $em->flush();
                unset($vente);
                unset($form);
                $vente = new Vente();
                $form = $this->createForm(AjouterVenteType::class, $vente);
                return $this->render('admin/adminAjouterVente.html.twig', array('form' => $form->createView(), 'error' => -1));
            }
        }
        return $this->render('admin/adminAjouterVente.html.twig', array('form' => $form->createView(), 'error' => 0));
    }

    /**
     * @Route("/admin-ventes", name="admin-ventes")
     */
    public function adminVentes(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $ventes = $this->getVentes();
        if ($session->get('venteSupprime') == 1) {
            $session->set('venteSupprime', 0);
            return $this->render('admin/adminVentes.html.twig', array('ventes'=>$ventes, 'error' => -1));
        }
        return $this->render('admin/adminVentes.html.twig',array('ventes'=>$ventes, 'error' => 0));
    }

    /**
     * @Route("/admin-modification-vente/{id}", name="admin-modification-vente")
     */
    public function adminModificationVente($id, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $session->set('idModifierVente', $id);
        return $this->redirectToRoute('admin-modifier-vente');
    }

    /**
     * @Route("/admin-modifier-vente", name="admin-modifier-vente")
     */
    public function adminModifierVente(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $vente = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findOneBy(['id' => $session->get('idModifierVente')]);
        if ($session->get('ancienNbVentes') == null) {
            $session->set('ancienNbVentes', $vente->getNbVentes());
        }
        if ($session->get('idAncienLivre') == null) {
            $session->set('idAncienLivre', $vente->getLivre()->getId());
        }
        $form = $this->createForm(ModifierVenteType::class, $vente);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $ancienLivre = null;
                $vente->setDate($form['date']->getData());
                $vente->setSource($form['source']->getData());
                $vente->setPrix($form['prix']->getData());
                if ($session->get('idAncienLivre') == $vente->getLivre()->getId()) {
                    $vente->getLivre()->setStock($vente->getLivre()->getStock() + $session->get('ancienNbVentes') - $vente->getNbVentes());
                }
                $vente->setNbVentes($form['nbVentes']->getData());
                $vente->setLivre($form['livre']->getData());
                if ($session->get('idAncienLivre') != $vente->getLivre()->getId()) {
                    $ancienLivre = $this->getDoctrine()->getRepository(\App\Entity\Livre::class)->findOneBy(['id' => $session->get('idAncienLivre')]);
                    if ($session->get('ancienNbVentes') != null) {
                        $ancienLivre->setStock($ancienLivre->getStock() + $session->get('ancienNbVentes'));
                    }
                    else {
                        $ancienLivre->setStock($ancienLivre->getStock() + $vente->getNbVentes());
                    }
                    $vente->getLivre()->setStock($vente->getLivre()->getStock() - $vente->getNbVentes());
                    $session->set('idModifierVente', $vente->getId());
                }
                $session->set('idAncienLivre', null);
                $session->set('ancienNbVentes', null);
                $em = $this->getDoctrine()->getManager();
                $em->persist($vente);
                if ($ancienLivre != null) {
                    $em->persist($ancienLivre);
                }
                $em->flush();
                unset($vente);
                unset($form);
                $vente = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findOneBy(['id' => $session->get('idModifierVente')]);
                $form = $this->createForm(ModifierVenteType::class, $vente);
                return $this->render('admin/adminModifierVente.html.twig', array('form' => $form->createView(), 'error' => -1));
            }
        }
        return $this->render('admin/adminModifierVente.html.twig', array('form' => $form->createView(), 'error' => 0));
    }

    /**
     * @Route("/admin-suppression-vente/{id}/{request}", name="admin-suppression-vente")
     */
    public function adminSuppressionVente($id, $request, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        if ($request == 0) {
            $session->set('idSupprimerVente', $id);
            return $this->redirectToRoute('admin-supprimer-vente');
        }
        if ($request == 1) {
            $em = $this->getDoctrine()->getManager();
            $vente = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findOneBy(['id' => $session->get('idSupprimerVente')]);
            $vente->getLivre()->setStock($vente->getLivre()->getStock() + $vente->getNbVentes());
            $em->remove($vente);
            $em->flush();
            $session->set('venteSupprime', 1);
            return $this->redirectToRoute('admin-ventes');
        }
    }

    /**
     * @Route("/admin-supprimer-vente", name="admin-supprimer-vente")
     */
    public function adminSupprimerVente(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $vente = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findOneBy(['id' => $session->get('idSupprimerVente')]);
        return $this->render('admin/adminSupprimerVente.html.twig', array('vente' => $vente));
    }

    /**
     * @Route("/admin-ventes-par-auteur", name="admin-ventes-par-auteur")
     */
    public function adminVentesParAuteur(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $auteur = [];
        $form = $this->createForm(ParAuteurType::class, $auteur);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $ventes = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findByAuteur($form['auteur']->getData());
                return $this->render('admin/adminVentes.html.twig',array('ventes'=>$ventes, 'error' => 0));
            }
        }
        return $this->render('admin/adminVentesParAuteur.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/admin-ventes-par-livre", name="admin-ventes-par-livre")
     */
    public function adminVentesParLivre(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $livre = [];
        $form = $this->createForm(ParLivreType::class, $livre);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $ventes = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findBy(['livre' => $form['livre']->getData()]);
                return $this->render('admin/adminVentes.html.twig',array('ventes'=>$ventes, 'error' => 0));
            }
        }
        return $this->render('admin/adminVentesParLivre.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/admin-ajouter-bon-de-depot", name="admin-ajouter-bon-de-depot")
     */
    public function adminAjouterBonDeDepot(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $bon = new BonDeDepot();
        $form = $this->createForm(AjouterBonType::class, $bon);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $bons = $this->getBons();
                foreach ($bons as $unBon) {
                    if ($unBon->getLivre()->getTitre() == $form['livre']->getData()->getTitre() && $unBon->getDestinataire() == $form['destinataire']->getData()) {
                        unset($bon);
                        unset($form);
                        $bon = new BonDeDepot();
                        $form = $this->createForm(AjouterBonType::class, $bon);
                        return $this->render('admin/adminAjouterBon.html.twig', array('form' => $form->createView(), 'error' => 1));
                    }
                }
                $bon->setDestinataire($form['destinataire']->getData());
                $bon->setNbEnvoyes($form['nbEnvoyes']->getData());
                $bon->setNbVendus(0);
                $bon->setLivre($form['livre']->getData());
                $bon->getLivre()->setStock($bon->getLivre()->getStock() - $bon->getNbEnvoyes());
                $em = $this->getDoctrine()->getManager();
                $em->persist($bon);
                $em->flush();
                unset($bon);
                unset($form);
                $bon = new BonDeDepot();
                $form = $this->createForm(AjouterBonType::class, $bon);
                return $this->render('admin/adminAjouterBon.html.twig', array('form' => $form->createView(), 'error' => -1));
            }
        }
        return $this->render('admin/adminAjouterBon.html.twig', array('form' => $form->createView(), 'error' => 0));
    }

    /**
     * @Route("/admin-bons-de-depot-par-livre", name="admin-bons-de-depot-par-livre")
     */
    public function adminBonsParLivre(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $livre = [];
        $form = $this->createForm(ParLivreType::class, $livre);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $bons = $this->getDoctrine()->getRepository(\App\Entity\BonDeDepot::class)->findBy(['livre' => $form['livre']->getData()]);
                return $this->render('admin/adminBons.html.twig',array('bons'=>$bons, 'error' => 0));
            }
        }
        return $this->render('admin/adminBonsParLivre.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/admin-bons-de-depot", name="admin-bons-de-depot")
     */
    public function adminBonsDeDepot(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $bons = $this->getBons();
        if ($session->get('bonSupprime') == 1) {
            $session->set('bonSupprime', 0);
            return $this->render('admin/adminBons.html.twig', array('bons'=>$bons, 'error' => -1));
        }
        return $this->render('admin/adminBons.html.twig',array('bons'=>$bons, 'error' => 0));
    }

    /**
     * @Route("/admin-modification-bon-de-depot/{id}", name="admin-modification-bon-de-depot")
     */
    public function adminModificationBonDeDepot($id, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $session->set('idModifierBon', $id);
        return $this->redirectToRoute('admin-modifier-bon-de-depot');
    }

    /**
     * @Route("/admin-modifier-bon-de-depot", name="admin-modifier-bon-de-depot")
     */
    public function adminModifierBonDeDepot(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $bon = $this->getDoctrine()->getRepository(\App\Entity\BonDeDepot::class)->findOneBy(['id' => $session->get('idModifierBon')]);
        if ($session->get('ancienNbEnvoyes') == null) {
            $session->set('ancienNbEnvoyes', $bon->getNbEnvoyes());
        }
        if ($session->get('idAncienLivre') == null) {
            $session->set('idAncienLivre', $bon->getLivre()->getId());
        }
        $form = $this->createForm(ModifierBonType::class, $bon);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $ancienLivre = null;
                $bon->setDestinataire($form['destinataire']->getData());
                if ($session->get('idAncienLivre') == $bon->getLivre()->getId()) {
                    $bon->getLivre()->setStock($bon->getLivre()->getStock() + $session->get('ancienNbEnvoyes') - $bon->getNbEnvoyes());
                }
                $bon->setNbEnvoyes($form['nbEnvoyes']->getData());
                $bon->setNbVendus($form['nbVendus']->getData());
                $bon->setLivre($form['livre']->getData());
                if ($session->get('idAncienLivre') != $bon->getLivre()->getId()) {
                    $ancienLivre = $this->getDoctrine()->getRepository(\App\Entity\Livre::class)->findOneBy(['id' => $session->get('idAncienLivre')]);
                    if ($session->get('ancienNbEnvoyes') != null) {
                        $ancienLivre->setStock($ancienLivre->getStock() + $session->get('ancienNbEnvoyes'));
                    }
                    else {
                        $ancienLivre->setStock($ancienLivre->getStock() + $bon->getNbEnvoyes());
                    }
                    $bon->getLivre()->setStock($bon->getLivre()->getStock() - $bon->getNbEnvoyes());
                    $session->set('idModifierBon', $bon->getId());
                }
                $session->set('idAncienLivre', null);
                $session->set('ancienNbEnvoyes', null);
                $em = $this->getDoctrine()->getManager();
                $em->persist($bon);
                if ($ancienLivre != null) {
                    $em->persist($ancienLivre);
                }
                $em->flush();
                unset($bon);
                unset($form);
                $bon = $this->getDoctrine()->getRepository(\App\Entity\BonDeDepot::class)->findOneBy(['id' => $session->get('idModifierBon')]);
                $form = $this->createForm(ModifierBonType::class, $bon);
                return $this->render('admin/adminModifierBon.html.twig', array('form' => $form->createView(), 'error' => -1));
            }
        }
        return $this->render('admin/adminModifierBon.html.twig', array('form' => $form->createView(), 'error' => 0));
    }

    /**
     * @Route("/admin-suppression-bon-de-depot/{id}/{request}", name="admin-suppression-bon-de-depot")
     */
    public function adminSuppressionBonDeDepot($id, $request, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        if ($request == 0) {
            $session->set('idSupprimerBon', $id);
            return $this->redirectToRoute('admin-supprimer-bon-de-depot');
        }
        if ($request == 1) {
            $em = $this->getDoctrine()->getManager();
            $bon = $this->getDoctrine()->getRepository(\App\Entity\BonDeDepot::class)->findOneBy(['id' => $session->get('idSupprimerBon')]);
            $bon->getLivre()->setStock($bon->getLivre()->getStock() + $bon->getNbEnvoyes() - $bon->getNbVendus());
            $em->remove($bon);
            $em->flush();
            $session->set('bonSupprime', 1);
            return $this->redirectToRoute('admin-bons-de-depot');
        }
    }

    /**
     * @Route("/admin-supprimer-bon-de-depot", name="admin-supprimer-bon-de-depot")
     */
    public function adminSupprimerBonDeDepot(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $bon = $this->getDoctrine()->getRepository(\App\Entity\BonDeDepot::class)->findOneBy(['id' => $session->get('idSupprimerBon')]);
        return $this->render('admin/adminSupprimerBon.html.twig', array('bon' => $bon));
    }

    /**
     * @Route("/admin-ajouter-service-presse", name="admin-ajouter-service-presse")
     */
    public function adminAjouterServicePresse(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $service = new ServicePresse();
        $form = $this->createForm(AjouterServiceType::class, $service);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $services = $this->getServices();
                foreach ($services as $unService) {
                    if ($unService->getLivre()->getTitre() == $form['livre']->getData()->getTitre() && $unService->getDestinataire() == $form['destinataire']->getData()) {
                        unset($unService);
                        unset($form);
                        $service = new ServicePresse();
                        $form = $this->createForm(AjouterServiceType::class, $service);
                        return $this->render('admin/adminAjouterService.html.twig', array('form' => $form->createView(), 'error' => 1));
                    }
                }
                $service->setDestinataire($form['destinataire']->getData());
                $service->setNbDonnes($form['nbDonnes']->getData());
                $service->setLivre($form['livre']->getData());
                $service->getLivre()->setStock($service->getLivre()->getStock() - $service->getNbDonnes());
                $em = $this->getDoctrine()->getManager();
                $em->persist($service);
                $em->flush();
                unset($service);
                unset($form);
                $service = new ServicePresse();
                $form = $this->createForm(AjouterServiceType::class, $service);
                return $this->render('admin/adminAjouterService.html.twig', array('form' => $form->createView(), 'error' => -1));
            }
        }
        return $this->render('admin/adminAjouterService.html.twig', array('form' => $form->createView(), 'error' => 0));
    }

    /**
     * @Route("/admin-services-presse", name="admin-services-presse")
     */
    public function adminServicesPresse(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $services = $this->getServices();
        if ($session->get('serviceSupprime') == 1) {
            $session->set('serviceSupprime', 0);
            return $this->render('admin/adminServices.html.twig', array('services'=>$services, 'error' => -1));
        }
        return $this->render('admin/adminServices.html.twig', array('services'=>$services, 'error' => 0));
    }

    /**
     * @Route("/admin-services-presse-par-livre", name="admin-service-presse-par-livre")
     */
    public function adminServicePresseParLivre(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $livre = [];
        $form = $this->createForm(ParLivreType::class, $livre);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $services = $this->getDoctrine()->getRepository(\App\Entity\ServicePresse::class)->findBy(['livre' => $form['livre']->getData()]);
                return $this->render('admin/adminServices.html.twig',array('services'=>$services, 'error' => 0));
            }
        }
        return $this->render('admin/adminBonsParLivre.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/admin-modification-service-presse/{id}", name="admin-modification-service-presse")
     */
    public function adminModificationServicePresse($id, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $session->set('idModifierService', $id);
        return $this->redirectToRoute('admin-modifier-service-presse');
    }

    /**
     * @Route("/admin-modifier-service-presse", name="admin-modifier-service-presse")
     */
    public function adminModifierServicePresse(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $service = $this->getDoctrine()->getRepository(\App\Entity\ServicePresse::class)->findOneBy(['id' => $session->get('idModifierService')]);
        if ($session->get('ancienNbDonnes') == null) {
            $session->set('ancienNbDonnes', $service->getNbDonnes());
        }
        if ($session->get('idAncienLivre') == null) {
            $session->set('idAncienLivre', $service->getLivre()->getId());
        }
        $form = $this->createForm(ModifierServiceType::class, $service);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $ancienLivre = null;
                $service->setDestinataire($form['destinataire']->getData());
                if ($session->get('idAncienLivre') == $service->getLivre()->getId()) {
                    $service->getLivre()->setStock($service->getLivre()->getStock() + $session->get('ancienNbDonnes') - $service->getNbDonnes());
                }
                $service->setNbDonnes($form['nbDonnes']->getData());
                $service->setLivre($form['livre']->getData());
                if ($session->get('idAncienLivre') != $service->getLivre()->getId()) {
                    $ancienLivre = $this->getDoctrine()->getRepository(\App\Entity\Livre::class)->findOneBy(['id' => $session->get('idAncienLivre')]);
                    if ($session->get('ancienNbDonnes') != null) {
                        $ancienLivre->setStock($ancienLivre->getStock() + $session->get('ancienNbDonnes'));
                    }
                    else {
                        $ancienLivre->setStock($ancienLivre->getStock() + $service->getNbDonnees());
                    }
                    $service->getLivre()->setStock($service->getLivre()->getStock() - $service->getNbDonnes());
                    $session->set('idModifierService', $service->getId());
                }
                $session->set('idAncienLivre', null);
                $session->set('ancienNbDonnes', null);
                $em = $this->getDoctrine()->getManager();
                $em->persist($service);
                if ($ancienLivre != null) {
                    $em->persist($ancienLivre);
                }
                $em->flush();
                unset($service);
                unset($form);
                $service = $this->getDoctrine()->getRepository(\App\Entity\ServicePresse::class)->findOneBy(['id' => $session->get('idModifierService')]);
                $form = $this->createForm(ModifierServiceType::class, $service);
                return $this->render('admin/adminModifierService.html.twig', array('form' => $form->createView(), 'error' => -1));
            }
        }
        /*
        $form = $this->createForm(ModifierServiceType::class, $service);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $service->setDestinataire($form['destinataire']->getData());
                $service->getLivre()->setStock($service->getLivre()->getStock() + $session->get('ancienNbDonnes') - $service->getNbDonnes());
                $session->set('ancienNbDonnes', null);
                $service->setNbDonnes($form['nbDonnes']->getData());
                $em = $this->getDoctrine()->getManager();
                $em->persist($service);
                $em->flush();
                unset($service);
                unset($form);
                $service = $this->getDoctrine()->getRepository(\App\Entity\ServicePresse::class)->findOneBy(['id' => $session->get('idModifierService')]);
                $form = $this->createForm(ModifierServiceType::class, $service);
                return $this->render('admin/adminModifierService.html.twig', array('form' => $form->createView(), 'error' => -1));
            }
        }*/
        return $this->render('admin/adminModifierService.html.twig', array('form' => $form->createView(), 'error' => 0));
    }

    /**
     * @Route("/admin-suppression-service-presse/{id}/{request}", name="admin-suppression-service-presse")
     */
    public function adminSuppressionServicePresse($id, $request, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        if ($request == 0) {
            $session->set('idSupprimerService', $id);
            return $this->redirectToRoute('admin-supprimer-service-presse');
        }
        if ($request == 1) {
            $em = $this->getDoctrine()->getManager();
            $service = $this->getDoctrine()->getRepository(\App\Entity\ServicePresse::class)->findOneBy(['id' => $session->get('idSupprimerService')]);
            $service->getLivre()->setStock($service->getLivre()->getStock() + $service->getNbDonnes());
            $em->remove($service);
            $em->flush();
            $session->set('serviceSupprime', 1);
            return $this->redirectToRoute('admin-services-presse');
        }
    }

    /**
     * @Route("/admin-supprimer-service-presse", name="admin-supprimer-service-presse")
     */
    public function adminSupprimerServicePresse(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $service = $this->getDoctrine()->getRepository(\App\Entity\ServicePresse::class)->findOneBy(['id' => $session->get('idSupprimerService')]);
        return $this->render('admin/adminSupprimerService.html.twig', array('service' => $service));
    }

    /**
     * @Route("/admin-ajouter-salon", name="admin-ajouter-salon")
     */
    public function adminAjouterSalon(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $salon = new Salon();
        $form = $this->createForm(AjouterSalonType::class, $salon);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $salons = $this->getSalons();
                foreach ($salons as $unSalon) {
                    if ($unSalon->getNom() == $form['nom']->getData() && $unSalon->getDate() == $form['date']->getData()) {
                        unset($salon);
                        unset($form);
                        $salon = new Salon();
                        $form = $this->createForm(AjouterSalonType::class, $salon);
                        return $this->render('admin/adminAjouterSalon.html.twig', array('form' => $form->createView(), 'error' => 1));
                    }
                }
                $salon->setNom($form['nom']->getData());
                $salon->setDate($form['date']->getData());
                $salon->setVille($form['ville']->getData());
                $em = $this->getDoctrine()->getManager();
                $em->persist($salon);
                $em->flush();
                unset($salon);
                unset($form);
                $salon = new Salon();
                $form = $this->createForm(AjouterSalonType::class, $salon);
                return $this->render('admin/adminAjouterSalon.html.twig', array('form' => $form->createView(), 'error' => -1));
            }
        }
        return $this->render('admin/adminAjouterSalon.html.twig', array('form' => $form->createView(), 'error' => 0));
    }

    /**
     * @Route("/admin-salons", name="admin-salons")
     */
    public function adminSalons(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $salons = $this->getSalons();
        if ($session->get('salonSupprime') == 1) {
            $session->set('salonSupprime', 0);
            return $this->render('admin/adminSalons.html.twig', array('salons'=>$salons, 'error' => -1));
        }
        return $this->render('admin/adminSalons.html.twig',array('salons'=>$salons, 'error' => 0));
    }

    /**
     * @Route("/admin-modification-salon/{id}", name="admin-modification-salon")
     */
    public function adminModificationSalon($id, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $session->set('idModifierSalon', $id);
        return $this->redirectToRoute('admin-modifier-salon');
    }

    /**
     * @Route("/admin-modifier-salon", name="admin-modifier-salon")
     */
    public function adminModifierSalon(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $salon = $this->getDoctrine()->getRepository(\App\Entity\Salon::class)->findOneBy(['id' => $session->get('idModifierSalon')]);
        $form = $this->createForm(ModifierSalonType::class, $salon);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $salon->setNom($form['nom']->getData());
                $salon->setDate($form['date']->getData());
                $salon->setVille($form['ville']->getData());
                $em = $this->getDoctrine()->getManager();
                $em->persist($salon);
                $em->flush();
                unset($salon);
                unset($form);
                $salon = $this->getDoctrine()->getRepository(\App\Entity\Salon::class)->findOneBy(['id' => $session->get('idModifierSalon')]);
                $form = $this->createForm(ModifierSalonType::class, $salon);
                return $this->render('admin/adminModifierSalon.html.twig', array('form' => $form->createView(), 'error' => -1));
            }
        }
        return $this->render('admin/adminModifierSalon.html.twig', array('form' => $form->createView(), 'error' => 0));
    }

    /**
     * @Route("/admin-suppression-salon/{id}/{request}", name="admin-suppression-salon")
     */
    public function adminSuppressionSalon($id, $request, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        if ($request == 0) {
            $session->set('idSupprimerSalon', $id);
            return $this->redirectToRoute('admin-supprimer-salon');
        }
        if ($request == 1) {
            $em = $this->getDoctrine()->getManager();
            $salon = $this->getDoctrine()->getRepository(\App\Entity\Salon::class)->findOneBy(['id' => $session->get('idSupprimerSalon')]);
            $participations = $this->getDoctrine()->getRepository(\App\Entity\Participation::class)->findBy(['salon' => $salon]);
            foreach($participations as $uneParticipation) {
                $em->remove($uneParticipation);
            }
            $em->remove($salon);
            $em->flush();
            $session->set('salonSupprime', 1);
            return $this->redirectToRoute('admin-salons');
        }
    }

    /**
     * @Route("/admin-supprimer-salon", name="admin-supprimer-salon")
     */
    public function adminSupprimerSalon(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $salon = $this->getDoctrine()->getRepository(\App\Entity\Salon::class)->findOneBy(['id' => $session->get('idSupprimerSalon')]);
        return $this->render('admin/adminSupprimerSalon.html.twig', array('salon' => $salon));
    }

    /**
     * @Route("/admin-get-id-salon/{id}", name="admin-get-id-salon")
     */
    public function adminGetIdSalon($id, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $session->set('idSalon', $id) ;
        return $this->redirectToRoute('admin-participations');
    }

    /**
     * @Route("/admin-participations", name="admin-participations")
     */
    public function adminParticipations(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $salon = $this->getDoctrine()->getRepository(\App\Entity\Salon::class)->findOneBy(['id' => $session->get('idSalon')]);
        $participations = $this->getParticipations($session->get('idSalon'));
        if ($session->get('participationSupprime') == 1) {
            $session->set('participationSupprime', 0);
            return $this->render('admin/adminParticipations.html.twig', array('salon' => $salon, 'participations' => $participations, 'error' => -1));
        }
        return $this->render('admin/adminParticipations.html.twig',array('salon' => $salon, 'participations' => $participations, 'error' => 0));
    }
    
    /**
     * @Route("/admin-suppression-participation/{id}/{request}", name="admin-suppression-participation")
     */
    public function adminSuppressionParticipation($id, $request, Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        if ($request == 0) {
            $session->set('idSupprimerParticipation', $id);
            return $this->redirectToRoute('admin-supprimer-participation');
        }
        if ($request == 1) {
            $em = $this->getDoctrine()->getManager();
            $participation = $this->getDoctrine()->getRepository(\App\Entity\Participation::class)->findOneBy(['id' => $session->get('idSupprimerParticipation')]);
            $em->remove($participation);
            $em->flush();
            $session->set('participationSupprime', 1);
            return $this->redirectToRoute('admin-participations');
        }
    }

    /**
     * @Route("/admin-supprimer-participation", name="admin-supprimer-participation")
     */
    public function adminSupprimerParticipation(Request $query, SessionInterface $session)
    {
        if ($session->get('admin') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $participation = $this->getDoctrine()->getRepository(\App\Entity\Participation::class)->findOneBy(['id' => $session->get('idSupprimerParticipation')]);
        return $this->render('admin/adminSupprimerParticipation.html.twig', array('participation' => $participation));
    }

    public function getLivres() {
        $livres = $this->getDoctrine()->getRepository(\App\Entity\Livre::class)->findAllByAuteur();
        return $livres;
    }

    public function getAuteurs() {
        $auteurs = $this->getDoctrine()->getRepository(\App\Entity\Auteur::class)->findBy(array(), array('nom' => 'asc'));
        return $auteurs;
    }

    public function getVentes() {
        $ventes = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findAllByAuteur();
        return $ventes;
    }

    public function getBons() {
        $ventes = $this->getDoctrine()->getRepository(\App\Entity\BonDeDepot::class)->findAllByAuteur();
        return $ventes;
    }

    public function getServices() {
        $ventes = $this->getDoctrine()->getRepository(\App\Entity\ServicePresse::class)->findAllByAuteur();
        return $ventes;
    }

    public function getSalons() {
        $salons = $this->getDoctrine()->getRepository(\App\Entity\Salon::class)->findBy(array(), array('date' => 'desc'));
        return $salons;
    }
    
    public function getParticipations($id) {
        $participations = $this->getDoctrine()->getRepository(\App\Entity\Participation::class)->findBySalon($id);
        return $participations;
    }
}
