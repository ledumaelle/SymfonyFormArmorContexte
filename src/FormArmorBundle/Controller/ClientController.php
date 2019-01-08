<?php

namespace FormArmorBundle\Controller;

use FormArmorBundle\Form\ClientType;
use FormArmorBundle\Form\ClientCompletType;
use FormArmorBundle\Form\StatutType;
use FormArmorBundle\Form\FormationType;
use FormArmorBundle\Form\InscriptionType;
use FormArmorBundle\Form\SessionType;
use FormArmorBundle\Form\PlanFormationType;

use FormArmorBundle\Entity\Client;
use FormArmorBundle\Entity\Formation;
use FormArmorBundle\Entity\Session_formation;
use FormArmorBundle\Entity\Session_Autorisees;
use FormArmorBundle\Entity\Plan_formation;
use FormArmorBundle\Entity\Inscription;
use FormArmorBundle\Entity\Statut;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

class ClientController extends Controller
{
    public function authentifAction(Request $request) // Affichage du formulaire d'authentification
    {
        
		// Création du formulaire
		$client = new Client();
		$form   = $this->get('form.factory')->create(ClientType::class, $client);
		
		
		// Contrôle du mdp si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				// Récupération des données saisies (le nom des controles sont du style nomDuFormulaire[nomDuChamp] (ex. : client[nom] pour le nom) )
				$donneePost = $request->request->get('client');
				$nom = $donneePost['nom'];
				$mdp = $donneePost['password'];
				
				// Controle du nom et du mdp
				$manager = $this->getDoctrine()->getManager();
				$rep = $manager->getRepository('FormArmorBundle:Client');
				$nbClient = $rep->verifMDP($nom, $mdp);
				$admin =$rep->verifAdmin($nom, $mdp)['admin'];
				if ($nbClient > 0 && ($admin ==true))
				{
					$utilisateur = $rep->findByLogin($nom, $mdp);
					$session = new Session();
					$session->set('utilisateur', $utilisateur);
					return $this->render('FormArmorBundle:Admin:accueil.html.twig');
				}
				else if ($nbClient > 0 && ($admin ==false)) 		
				{		
					$utilisateur = $rep->findByLogin($nom, $mdp);
					$session = new Session();
					$session->set('utilisateur', $utilisateur);
					return $this->render('FormArmorBundle:Client:accueil.html.twig');
				}
				$request->getSession()->getFlashBag()->add('connection', 'Login ou mot de passe incorrects');
			}
		}
		
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Client:connexion.html.twig', array('form' => $form->createView()));
	}
	
	// Gestion des sessions
	public function listeSessionAction($page,Request $request)
	{
		$session = $request->getSession();
		$client = $session->get('utilisateur');
		$idClient = $client->getId();

		if ($page < 1)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

		// On peut fixer le nombre de lignes avec la ligne suivante :
		// $nbParPage = 4;
		// Mais bien sûr il est préférable de définir un paramètre dans "app\config\parameters.yml", et d'y accéder comme ceci :
		$nbParPage = $this->container->getParameter('nb_par_page');
		
		// On récupère l'objet Paginator
		$manager = $this->getDoctrine()->getManager();
		$rep = $manager->getRepository('FormArmorBundle:Sessions_Autorisees');
		
		$lesSessions = $rep->listeSessionsClient($page, $nbParPage,$idClient);

		// On calcule le nombre total de pages grâce au count($lesSessions) qui retourne le nombre total de sessions
		$nbPages = ceil(count($lesSessions) / $nbParPage);
		
		// Si la page n'existe pas, on retourne une erreur 404
		if ($page > $nbPages)
		{
			return $this->render('FormArmorBundle:Client:session.html.twig',array(
			'lesSessions' => null,
			'nbPages' => 1,
			'page' => 1));
			//throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		// On donne toutes les informations nécessaires à la vue
		return $this->render('FormArmorBundle:Client:session.html.twig', array(
		  'lesSessions' => $lesSessions,
		  'nbPages'     => $nbPages,
		  'page'        => $page,
		));
	}
	public function inscriptionSessionAction($id, Request $request)
    {
		$session = $request->getSession();
		$client = $session->get('utilisateur');
		$idClient = $client->getId();

		// Récupération de la formation d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Sessions_Autorisees');
		$sessionAutorisee = $rep->findBySession($id);
		$rep = $em->getRepository('FormArmorBundle:Session_formation');
		$sessionFormation = $rep->find($sessionAutorisee->getSession());
		$rep = $em->getRepository('FormArmorBundle:Plan_formation');
		$plan = $rep->findByClientAndFormation($client,$sessionFormation->getFormation()->getId());
		$rep2 = $em->getRepository('FormArmorBundle:Client');
		$client = $rep2->find($idClient);

		// Création du formulaire
		$inscription = new Inscription();
		$date = new \DateTime("NOW");
		$inscription->setDateInscription($date);
		$inscription->setClient($client);
		$inscription->setSessionFormation($sessionFormation);	
		$form   = $this->get('form.factory')->create(InscriptionType::class, $inscription);
		// Contrôle du mdp si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				$rep = $em->getRepository('FormArmorBundle:Inscription');
				$nbClientSession = $rep->findByClientAndSession($client,$sessionFormation);

				if($nbClientSession == 0)
				{
					$sessionFormation->setNbInscrits($sessionFormation->getNbInscrits() +1);
					$plan->setEffectue(1);
					// mise à jour de la bdd
					$em->persist($inscription);
					$em->flush();

					// Réaffichage de la liste des sessions
					$nbParPage = $this->container->getParameter('nb_par_page');
					// On récupère l'objet Paginator
					$rep = $em->getRepository('FormArmorBundle:Sessions_Autorisees');
					$lesSessions = $rep->listeSessionsClient(1, $nbParPage,$idClient);
					
					// On calcule le nombre total de pages grâce au count($lesSessions) qui retourne le nombre total de sessions
					$nbPages = ceil(count($lesSessions) / $nbParPage);
						
					if (1 > $nbPages)
					{
						return $this->render('FormArmorBundle:Client:session.html.twig',array(
						'lesSessions' => null,
						'nbPages' => 1,
						'page' => 1));
					}
					// On donne toutes les informations nécessaires à la vue
					return $this->render('FormArmorBundle:Client:session.html.twig', array(
					'lesSessions' => $lesSessions,
					'nbPages'     => $nbPages,
					'page'        => 1,
					));	
				}
				else 
				{
					$request->getSession()->getFlashBag()->add('info','Inscription déjà existante');
				}
				
			}
			
		}		
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Client:inscriptionSession.html.twig', array('form' => $form->createView()));
    }

	public function historiqueAction($page,Request $request)
	{
		$session = $request->getSession();
		$client = $session->get('utilisateur');
		$idClient = $client->getId();

		if ($page < 1)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

		// On peut fixer le nombre de lignes avec la ligne suivante :
		// $nbParPage = 4;
		// Mais bien sûr il est préférable de définir un paramètre dans "app\config\parameters.yml", et d'y accéder comme ceci :
		$nbParPage = $this->container->getParameter('nb_par_page');
		
		// On récupère l'objet Paginator
		$manager = $this->getDoctrine()->getManager();
		$rep = $manager->getRepository('FormArmorBundle:Inscription');
		
		$lesSessions = $rep->listeHistorique($page, $nbParPage,$client);
		
		// On calcule le nombre total de pages grâce au count($lesSessions) qui retourne le nombre total de sessions
		$nbPages = ceil(count($lesSessions) / $nbParPage);
		
		if ($page > $nbPages)
		{
			return $this->render('FormArmorBundle:Client:historique.html.twig',array(
			'lesSessions' => null,
			'nbPages' => 1,
			'page' => 1));
		}
		
		// On donne toutes les informations nécessaires à la vue
		return $this->render('FormArmorBundle:Client:historique.html.twig', array(
		  'lesSessions' => $lesSessions,
		  'nbPages'     => $nbPages,
		  'page'        => $page,
		));
	}
	
	public function deconnexionAction(Request $request) // Déconnexion
    {
		$session = $request->getSession();
		$session->clear();    
		   
   		return $this->render('FormArmorBundle:Accueil:index.html.twig');
    }
}
