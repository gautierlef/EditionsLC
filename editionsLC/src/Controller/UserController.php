<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Participation;
use App\Form\ModifierMdpType;
use App\Form\AjouterParticipationType;
use App\Form\ParAuteurLivreType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends AbstractController
{
    /**
     * @Route("/auteur", name="auteur")
     */
    public function auteur(SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $auteur = $this->getDoctrine()->getRepository(\App\Entity\Auteur::class)->findOneBy(['id' => $session->get('id')]);
        if (strlen($auteur->getMdp()) == 6 && substr($auteur->getMdp(), 0, 2) == strtolower(substr($auteur->getPrenom(), 0, 1)).strtoupper(substr($auteur->getNom(), 0, 1)) && ctype_digit(substr($auteur->getMdp(), -4))) {
            return $this->redirectToRoute('auteur-modifier-mdp');
        }
        return $this->render('auteur/auteur.html.twig');
    }
    
    /**
     * @Route("/auteur-livres", name="auteur-livres")
     */
    public function auteurLivres(Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $livres = $this->getLivres($session->get('id'));
        return $this->render('auteur/auteurLivres.html.twig',array('livres'=>$livres));
    }
    
    /**
     * @Route("/auteur-get-id-livre/{id}", name="auteur-get-id-livre")
     */
    public function auteurGetIdLivre($id, Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $session->set('idLivre', $id) ;
        return $this->redirectToRoute('auteur-ventes-livre');
    }
    
    /**
     * @Route("/auteur-ventes-livre", name="auteur-ventes-livre")
     */
    public function auteurVentesLivre(Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $ventes = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findBy(['livre' => $session->get('idLivre')]);
        return $this->render('auteur/auteurVentes.html.twig',array('ventes'=>$ventes));
    }
    
    /**
     * @Route("/auteur-ventes", name="auteur-ventes")
     */
    public function auteurVentes(Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $ventes = $this->getVentes($session->get('id'));
        return $this->render('auteur/auteurVentes.html.twig',array('ventes'=>$ventes));
    }
    
    /**
     * @Route("/auteur-ventes-par-livre", name="auteur-ventes-par-livre")
     */
    public function auteurVentesParLivre(Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $livre = [];
        $form = $this->createForm(ParAuteurLivreType::class, $livre, array('id' => $session->get('id')));
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $ventes = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findBy(['livre' => $form['livre']->getData()]);
                return $this->render('auteur/auteurVentes.html.twig',array('ventes'=>$ventes));
            }
        }
        return $this->render('auteur/auteurVentesParLivre.html.twig', array('form' => $form->createView()));
    }
    
    /**
     * @Route("/auteur-salons", name="auteur-salons")
     */
    public function auteurSalons(Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $salons = $this->getSalons();
        return $this->render('auteur/auteurSalons.html.twig',array('salons'=>$salons));
    }
    
    /**
     * @Route("/auteur-participations", name="auteur-participations")
     */
    public function auteurParticipations(Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $participations = $this->getParticipations();
        if ($session->get('participationAnnulee') == 1) {
            $session->set('participationAnnulee', 0);
            return $this->render('auteur/auteurParticipations.html.twig',array('participations'=>$participations, 'date' => date('Y-m-d', time()), 'error' => -1));
        }
        return $this->render('auteur/auteurParticipations.html.twig',array('participations'=>$participations, 'date' => date('Y-m-d', time()), 'error' => 0));
    }
    
    /**
     * @Route("/auteur-participer", name="auteur-participer")
     */
    public function auteurParticiper(Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $participation = new Participation();
        $salon = $this->getDoctrine()->getRepository(\App\Entity\Salon::class)->findOneBy(['id' => $session->get('idSalon')]);
        $form = $this->createForm(AjouterParticipationType::class, $participation, array('id' => $session->get('id')));
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $participations = $this->getParticipations();
                $auteur = $this->getDoctrine()->getRepository(\App\Entity\Auteur::class)->findOneBy(['id' => $session->get('id')]);
                foreach ($participations as $uneParticipation) {
                    if ($uneParticipation->getSalon() == $salon && $uneParticipation->getAuteur() == $auteur && $uneParticipation->getLivre() == $form['livre']->getData()) {
                        unset($participation);
                        unset($form);
                        $participation = new Participation();
                        $form = $this->createForm(AjouterParticipationType::class, $participation, array('id' => $session->get('id')));
                        return $this->render('auteur/auteurAjouterParticipation.html.twig', array('salon' => $salon, 'form' => $form->createView(), 'error' => 1));
                    }
                }
                $participation->setSalon($salon);
                $participation->setAuteur($auteur);
                $participation->setLivre($form['livre']->getData());
                $em = $this->getDoctrine()->getManager();
                $em->persist($participation);
                $em->flush();
                unset($participation);
                unset($form);
                $participation = new Participation();
                $form = $this->createForm(AjouterParticipationType::class, $participation, array('id' => $session->get('id')));
                return $this->render('auteur/auteurAjouterParticipation.html.twig', array('salon' => $salon, 'form' => $form->createView(), 'error' => -1));
            }
        }
        return $this->render('auteur/auteurAjouterParticipation.html.twig', array('salon' => $salon, 'form' => $form->createView(), 'error' => 0));
    }
    
    /**
     * @Route("/auteur-annulation-participation/{id}/{request}", name="auteur-annulation-participation")
     */
    public function auteurAnnulationParticipation($id, $request, Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        if ($request == 0) {
            $session->set('idAnnulerParticipation', $id);
            return $this->redirectToRoute('auteur-annuler-participation');
        }
        if ($request == 1) {
            $em = $this->getDoctrine()->getManager();
            $participation = $this->getDoctrine()->getRepository(\App\Entity\Participation::class)->findOneBy(['id' => $session->get('idAnnulerParticipation')]);
            $em->remove($participation);
            $em->flush();
            $session->set('participationAnnulee', 1);
            return $this->redirectToRoute('auteur-participations');
        }
    }
    
    /**
     * @Route("/auteur-annuler-participation", name="auteur-annuler-participation")
     */
    public function auteurAnnulerParticipation(Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $participation = $this->getDoctrine()->getRepository(\App\Entity\Participation::class)->findOneBy(['id' => $session->get('idAnnulerParticipation')]);
        return $this->render('auteur/auteurAnnulerParticipation.html.twig', array('participation' => $participation));
    }
    
    /**
     * @Route("/auteur-get-id-salon/{id}/{request}", name="auteur-get-id-salon")
     */
    public function auteurGetIdSalon($id, $request, Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        if ($request == 1) {
            $session->set('idSalon', $id);
            return $this->redirectToRoute('auteur-participer');
        }
    }
    
    /**
     * @Route("/auteur-modifier-mdp", name="auteur-modifier-mdp")
     */
    public function auteurModifierMdp(Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $mdp = [];
        $auteur = $this->getDoctrine()->getRepository(\App\Entity\Auteur::class)->findOneBy(['id' => $session->get('id')]);
        $form = $this->createForm(ModifierMdpType::class, $mdp);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                if ($form['ancienMdp']->getData() != $auteur->getMdp()) {
                    unset($form);
                    $mdp = [];
                    $form = $this->createForm(ModifierMdpType::class, $mdp);
                    return $this->render('auteur/auteurModifierMdp.html.twig', array('form' => $form->createView(), 'error' => 3));
                }
                if (strlen($form['nouveauMdp']->getData()) < 6) {
                    unset($form);
                    $mdp = [];
                    $form = $this->createForm(ModifierMdpType::class, $mdp);
                    return $this->render('auteur/auteurModifierMdp.html.twig', array('form' => $form->createView(), 'error' => 6));
                }
                if (preg_match('/[A-Z]/', $form['nouveauMdp']->getData()) == false || preg_match('/[a-z]/', $form['nouveauMdp']->getData()) == false || preg_match('/[0-9]/', $form['nouveauMdp']->getData()) == false) {
                    unset($form);
                    $mdp = [];
                    $form = $this->createForm(ModifierMdpType::class, $mdp);
                    return $this->render('auteur/auteurModifierMdp.html.twig', array('form' => $form->createView(), 'error' => 5));
                }
                if ($form['nouveauMdp']->getData() == $form['ancienMdp']->getData()) {
                    unset($form);
                    $mdp = [];
                    $form = $this->createForm(ModifierMdpType::class, $mdp);
                    return $this->render('auteur/auteurModifierMdp.html.twig', array('form' => $form->createView(), 'error' => 1));
                }
                if ($form['nouveauMdp']->getData() != $form['confirmerMdp']->getData()) {
                    unset($form);
                    $auteur = $this->getDoctrine()->getRepository(\App\Entity\Auteur::class)->findOneBy(['id' => $session->get('id')]);
                    $mdp = [];
                    $form = $this->createForm(ModifierMdpType::class, $mdp);
                    return $this->render('auteur/auteurModifierMdp.html.twig', array('form' => $form->createView(), 'error' => 2));
                }
                $auteur->setMdp($form['nouveauMdp']->getData());
                $em = $this->getDoctrine()->getManager();
                $em->persist($auteur);
                $em->flush();
                unset($form);
                $mdp = [];
                $form = $this->createForm(ModifierMdpType::class, $mdp);
                return $this->render('auteur/auteurModifierMdp.html.twig', array('form' => $form->createView(), 'error' => -1));
            }
        }
        if (strlen($auteur->getMdp()) == 6 && substr($auteur->getMdp(), 0, 2) == strtolower(substr($auteur->getPrenom(), 0, 1)).strtoupper(substr($auteur->getNom(), 0, 1)) && ctype_digit(substr($auteur->getMdp(), -4))) {
            return $this->render('auteur/auteurModifierMdp.html.twig', array('form' => $form->createView(), 'error' => 4));
        }
        return $this->render('auteur/auteurModifierMdp.html.twig', array('form' => $form->createView(), 'error' => 0));
    }
    
    /**
     * @Route("/auteur-graphique-ventes", name="auteur-graphique-ventes")
     */
    public function auteurGraphiqueVentes(Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $ventes = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findSum($session->get('id'));
        $periodes = array();
        foreach ($ventes as $uneVente) {
            if (in_array($uneVente['date'], $periodes) == false) {
                array_push($periodes, $uneVente['date']);
            }
        }
        $livres = array();
        foreach ($ventes as $uneVente) {
            if (in_array($uneVente['titre'], $livres) == false) {
                array_push($livres, $uneVente['titre']);
            }
        }
        $nbVentes = 0;
        $ventesModif = array();
        foreach ($periodes as $unePeriode) {
            foreach ($livres as $unLivre) {
                foreach ($ventes as $uneVente) {
                    if ($unePeriode == $uneVente['date'] && $unLivre == $uneVente['titre']) {
                        $nbVentes = $uneVente['nbVentes'];
                    }
                }
                array_push($ventesModif, $nbVentes);
                $nbVentes = 0;
            }
        }
        return $this->render('auteur/auteurGraphiqueVentes.html.twig', array('ventes' => $ventes, 'periodes' => $periodes, 'livres' => $livres, 'ventesModif' => $ventesModif));
    }
    
    /**
     * @Route("/auteur-graphique-ventes-2", name="auteur-graphique-ventes-2")
     */
    public function auteurGraphiqueVentesPie(Request $query, SessionInterface $session)
    {
        if ($session->get('id') == null) {
            return $this->redirectToRoute('session-expiree');
        }
        $ventes = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findSumNbVentes($session->get('id'));
        $livres = array();
        $total = 0;
        foreach ($ventes as $uneVente) {
            if (in_array($uneVente['titre'], $livres) == false) {
                array_push($livres, [$uneVente['titre'], $uneVente['nbVentes']]);
                $total = $total + $uneVente['nbVentes'];
            }
        }
        return $this->render('auteur/auteurGraphiqueVentesPie.html.twig', array('livres' => $livres, 'total' => $total));
    }
    
    public function getLivres($id) {
        $livres = $this->getDoctrine()->getRepository(\App\Entity\Livre::class)->findByAuteur($id);
        return $livres;
    }
    
    public function getVentes($id) {
        $ventes = $this->getDoctrine()->getRepository(\App\Entity\Vente::class)->findByAuteur($id);
        return $ventes;
    }
    
    public function getSalons() {
        $salons = $this->getDoctrine()->getRepository(\App\Entity\Salon::class)->findByDate(date('Y/m/d', time()));
        return $salons;
    }
    
    public function getParticipations() {
        $participations = $this->getDoctrine()->getRepository(\App\Entity\Participation::class)->findOrderByDate();
        return $participations;
    }
}
