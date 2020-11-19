<?php

namespace App\Controller;

use PDO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ConnexionType;
use App\Entity\Auteur;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AccueilController extends AbstractController
{
    /**
     * @Route("/", name="")
     */
    public function home(SessionInterface $session)
    {
        return $this->redirectToRoute('accueil');
    }
    
    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil(SessionInterface $session)
    {
        $session->clear();
        return $this->render('accueil/accueil.html.twig');
    }
    
    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion(Request $query)
    {
        $session = new Session();
        $auteur = new Auteur();
        $form = $this->createForm(ConnexionType::class, $auteur);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                if ($form['pseudo']->getData() == "AdminCécile" && $form['mdp']->getData() == "azerty") {
                    $session->set('admin', 1);
                    $session->set('pseudo', $form['pseudo']->getData());
                    return $this->redirectToRoute('admin');
                }
                $auteurs = $this->getAuteurs();
                foreach ($auteurs as $auteur) {
                    if ($auteur->getPseudo() == $form['pseudo']->getData() && $auteur->getMdp() == $form['mdp']->getData()) {
                        $session->set('pseudo', $form['pseudo']->getData());
                        $session->set('nom', $auteur->getNom());
                        $session->set('prenom', $auteur->getPrenom());
                        $session->set('id', $auteur->getId());
                        return $this->redirectToRoute('auteur');
                    }
                }
            }
            return $this->render('accueil/connexion.html.twig', array('form' => $form->createView(), 'error' => 1));
        }
        return $this->render('accueil/connexion.html.twig', array('form' => $form->createView(), 'error' => 0));
    }
    
    /**
     * @Route("/session-expiree", name="session-expiree")
     */
    public function sessionExpiree()
    {
        return $this->render('accueil/sessionExpiree.html.twig');
    }
    
    public function getAuteurs() {
        $auteurs = $this->getDoctrine()->getRepository(\App\Entity\Auteur::class)->findAll();
        return $auteurs;
    }
}
