<?php

namespace FormArmorBundle\Controller;

use FormArmorBundle\Form\ClientType;
use FormArmorBundle\Form\ClientCompletType;
use FormArmorBundle\Form\StatutType;
use FormArmorBundle\Form\FormationType;
use FormArmorBundle\Form\SessionType;
use FormArmorBundle\Form\PlanFormationType;

use FormArmorBundle\Entity\Client;
use FormArmorBundle\Entity\Formation;
use FormArmorBundle\Entity\Session_formation;
use FormArmorBundle\Entity\Plan_formation;
use FormArmorBundle\Entity\Statut;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class AdminController extends Controller
{
    	
	// Gestion des statuts
	public function listeStatutAction($page)
	{
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
		$rep = $manager->getRepository('FormArmorBundle:Statut');
		$lesStatuts = $rep->listeStatuts($page, $nbParPage);
		
		// On calcule le nombre total de pages grâce au count($lesStatuts) qui retourne le nombre total de statuts
		$nbPages = ceil(count($lesStatuts) / $nbParPage);
		
		// Si la page n'existe pas, on retourne une erreur 404
		if ($page > $nbPages)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		// On donne toutes les informations nécessaires à la vue
		return $this->render('FormArmorBundle:Admin:statut.html.twig', array(
		  'lesStatuts' => $lesStatuts,
		  'nbPages'     => $nbPages,
		  'page'        => $page,
		));
	}
	public function modifStatutAction($id, Request $request) // Affichage du formulaire de modification d'un statut
    {
        // Récupération du statut d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Statut');
		$statut = $rep->find($id);
		
		// Création du formulaire à partir du statut "récupéré"
		$form   = $this->get('form.factory')->create(StatutType::class, $statut);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				// mise à jour de la bdd
				$em->persist($statut);
				$em->flush();
				
				// Réaffichage de la liste des statuts
				$nbParPage = $this->container->getParameter('nb_par_page');
				// On récupère l'objet Paginator
				$lesStatuts = $rep->listeStatuts(1, $nbParPage);
				
				// On calcule le nombre total de pages grâce au count($lesStatuts) qui retourne le nombre total de statuts
				$nbPages = ceil(count($lesStatuts) / $nbParPage);
				
				// On donne toutes les informations nécessaires à la vue
				return $this->render('FormArmorBundle:Admin:statut.html.twig', array(
				  'lesStatuts' => $lesStatuts,
				  'nbPages'     => $nbPages,
				  'page'        => 1,
				));
			}
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formStatut.html.twig', array('form' => $form->createView(), 'action' => 'modification'));
    }
	public function suppStatutAction($id, Request $request) // Affichage du formulaire de suppression d'un statut
    {
        // Récupération du statut d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Statut');
		$statut = $rep->find($id);
		
		// Création du formulaire à partir du statut "récupéré"
		$form   = $this->get('form.factory')->create(StatutType::class, $statut);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			
			// Récupération de l'identifiant du statut à supprimer
			$donneePost = $request->request->get('statut');
			//$identif = $donneePost['id'];
			
			// mise à jour de la bdd
			$res = $rep->suppStatut($id);
			$em->persist($statut);
			$em->flush();
				
			// Réaffichage de la liste des statuts
			$nbParPage = $this->container->getParameter('nb_par_page');
			// On récupère l'objet Paginator
			$lesStatuts = $rep->listeStatuts(1, $nbParPage);
				
			// On calcule le nombre total de pages grâce au count($lesFormations) qui retourne le nombre total de formations
			$nbPages = ceil(count($lesStatuts) / $nbParPage);
				
			// On donne toutes les informations nécessaires à la vue
			return $this->render('FormArmorBundle:Admin:statut.html.twig', array(
				'lesStatuts' => $lesStatuts,
				'nbPages'     => $nbPages,
				'page'        => 1,
				));
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formStatut.html.twig', array('form' => $form->createView(), 'action' => 'SUPPRESSION'));
    }
	
	// Gestion des clients
	public function listeClientAction($page)
	{
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
		$rep = $manager->getRepository('FormArmorBundle:Client');
		$lesClients = $rep->listeClients($page, $nbParPage);
		
		// On calcule le nombre total de pages grâce au count($lesClients) qui retourne le nombre total de clients
		$nbPages = ceil(count($lesClients) / $nbParPage);
		
		// Si la page n'existe pas, on retourne une erreur 404
		if ($page > $nbPages)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		// On donne toutes les informations nécessaires à la vue
		return $this->render('FormArmorBundle:Admin:client.html.twig', array(
		  'lesClients' => $lesClients,
		  'nbPages'     => $nbPages,
		  'page'        => $page,
		));
	}
	public function modifClientAction($id, Request $request) // Affichage du formulaire de modification d'un statut
    {
        // Récupération du client d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Client');
		$client = $rep->find($id);
		
		// Création du formulaire à partir du client "récupéré"
		$form   = $this->get('form.factory')->create(ClientCompletType::class, $client);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				// mise à jour de la bdd
				$em->persist($client);
				$em->flush();
				
				// Réaffichage de la liste des clients
				$nbParPage = $this->container->getParameter('nb_par_page');
				// On récupère l'objet Paginator
				$lesClients = $rep->listeClients(1, $nbParPage);
				
				// On calcule le nombre total de pages grâce au count($lesClients) qui retourne le nombre total de clients
				$nbPages = ceil(count($lesClients) / $nbParPage);
				
				// On donne toutes les informations nécessaires à la vue
				return $this->render('FormArmorBundle:Admin:client.html.twig', array(
				  'lesClients' => $lesClients,
				  'nbPages'     => $nbPages,
				  'page'        => 1,
				));
			}
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formClient.html.twig', array('form' => $form->createView(), 'action' => 'modification'));
    }
	public function suppClientAction($id, Request $request) // Affichage du formulaire de suppression d'un client
    {
        // Récupération du client d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Client');
		$client = $rep->find($id);
		
		// Création du formulaire à partir du client "récupéré"
		$form   = $this->get('form.factory')->create(ClientCompletType::class, $client);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			
			// Récupération de l'identifiant du client à supprimer
			$donneePost = $request->request->get('client');
			
			// mise à jour de la bdd
			$res = $rep->suppClient($id);
			$em->persist($client);
			$em->flush();
				
			// Réaffichage de la liste des clients
			$nbParPage = $this->container->getParameter('nb_par_page');
			// On récupère l'objet Paginator
			$lesClients = $rep->listeClients(1, $nbParPage);
				
			// On calcule le nombre total de pages grâce au count($lesClients) qui retourne le nombre total de clients
			$nbPages = ceil(count($lesClients) / $nbParPage);
				
			// On donne toutes les informations nécessaires à la vue
			return $this->render('FormArmorBundle:Admin:client.html.twig', array(
				'lesClients' => $lesClients,
				'nbPages'     => $nbPages,
				'page'        => 1,
				));
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formClient.html.twig', array('form' => $form->createView(), 'action' => 'SUPPRESSION'));
    }
	
	// Gestion des formations
	public function listeFormationAction($page)
	{
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
		$rep = $manager->getRepository('FormArmorBundle:Formation');
		$lesFormations = $rep->listeFormations($page, $nbParPage);
		
		// On calcule le nombre total de pages grâce au count($lesFormations) qui retourne le nombre total de formations
		$nbPages = ceil(count($lesFormations) / $nbParPage);
		
		// Si la page n'existe pas, on retourne une erreur 404
		if ($page > $nbPages)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		// On donne toutes les informations nécessaires à la vue
		return $this->render('FormArmorBundle:Admin:formation.html.twig', array(
		  'lesFormations' => $lesFormations,
		  'nbPages'     => $nbPages,
		  'page'        => $page,
		));
	}
	public function modifFormationAction($id, Request $request) // Affichage du formulaire de modification d'une formation
    {
        // Récupération de la formation d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Formation');
		$formation = $rep->find($id);
		
		// Création du formulaire à partir de la formation "récupérée"
		$form   = $this->get('form.factory')->create(FormationType::class, $formation);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				// mise à jour de la bdd
				$em->persist($formation);
				$em->flush();
				
				// Réaffichage de la liste des clients
				$nbParPage = $this->container->getParameter('nb_par_page');
				// On récupère l'objet Paginator
				$lesFormations = $rep->listeFormations(1, $nbParPage);
				
				// On calcule le nombre total de pages grâce au count($lesFormations) qui retourne le nombre total de formations
				$nbPages = ceil(count($lesFormations) / $nbParPage);
				
				// On donne toutes les informations nécessaires à la vue
				return $this->render('FormArmorBundle:Admin:formation.html.twig', array(
				  'lesFormations' => $lesFormations,
				  'nbPages'     => $nbPages,
				  'page'        => 1,
				));
			}
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formFormation.html.twig', array('form' => $form->createView(), 'action' => 'modification'));
    }
	public function suppFormationAction($id, Request $request) // Affichage du formulaire de suppression d'une formation
    {
        // Récupération de la formation d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Formation');
		$formation = $rep->find($id);
		
		// Création du formulaire à partir de la formation "récupérée"
		$form   = $this->get('form.factory')->create(FormationType::class, $formation);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			
			// Récupération de l'identifiant de la formation à supprimer
			$donneePost = $request->request->get('formation');
			
			// mise à jour de la bdd
			$res = $rep->suppFormation($id);
			$em->persist($formation);
			$em->flush();
				
			// Réaffichage de la liste des formations
			$nbParPage = $this->container->getParameter('nb_par_page');
			// On récupère l'objet Paginator
			$lesFormations = $rep->listeFormations(1, $nbParPage);
				
			// On calcule le nombre total de pages grâce au count($lesFormations) qui retourne le nombre total de formations
			$nbPages = ceil(count($lesFormations) / $nbParPage);
				
			// On donne toutes les informations nécessaires à la vue
			return $this->render('FormArmorBundle:Admin:formation.html.twig', array(
				'lesFormations' => $lesFormations,
				'nbPages'     => $nbPages,
				'page'        => 1,
				));
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formFormation.html.twig', array('form' => $form->createView(), 'action' => 'SUPPRESSION'));
    }
	
	// Gestion des sessions
	public function listeSessionAction($page)
	{
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
		$rep = $manager->getRepository('FormArmorBundle:Session_formation');
		$lesSessions = $rep->listeSessions($page, $nbParPage);
		
		// On calcule le nombre total de pages grâce au count($lesSessions) qui retourne le nombre total de sessions
		$nbPages = ceil(count($lesSessions) / $nbParPage);

		// Si la page n'existe pas, on retourne une erreur 404
		if ($page > $nbPages)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		// On donne toutes les informations nécessaires à la vue
		return $this->render('FormArmorBundle:Admin:session.html.twig', array(
		  'lesSessions' => $lesSessions,
		  'nbPages'     => $nbPages,
		  'page'        => $page,
		));
	}
	public function modifSessionAction($id, Request $request) // Affichage du formulaire de modification d'une session
    {
        // Récupération de la formation d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Session_formation');
		$session = $rep->find($id);

		// Création du formulaire à partir de la session "récupérée"
		$form   = $this->get('form.factory')->create(SessionType::class, $session);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				// mise à jour de la bdd
				$em->persist($session);
				$em->flush();
				
				if($session->getClose() == 1){
					$transport = (new \Swift_SmtpTransport($this->container->getParameter('mailer_host'), $this->container->getParameter('mailer_port'), $this->container->getParameter('mailer_encryption')))
					->setUsername($this->container->getParameter('mailer_user'))
					->setPassword($this->container->getPArameter('mailer_password'));
				
					$manager = $this->getDoctrine()->getManager();
					$repInscription = $manager->getRepository('FormArmorBundle:Inscription');
			
					$imageEntete = \Swift_Image::fromPath('../web/images/banniere.jpg');
					$imageCarte = \Swift_Image::fromPath('../web/images/carte_Formarmor.jpg');
					$imageValise = \Swift_Image::fromPath('../web/images/valise.jpg');
					if($session->getFormation()->getTypeForm() == "Compta"){
						$imageType = \Swift_Image::fromPath('../web/images/compta.jpg');
					}else{
						$imageType = \Swift_Image::fromPath('../web/images/bur.jpg');
					}
					if($session->getFormation()->getDiplomante() == 1){
						$imageDipl = \Swift_Image::fromPath('../web/images/dipl.jpg');
					}else{
						$imageDipl = \Swift_Image::fromPath('../web/images/nonDipl.jpg');
					}
					$imageCalendrier = \Swift_Image::fromPath('../web/images/calendrier.jpg');
					$imageDuree = \Swift_Image::fromPath('../web/images/duree.jpg');

					$mailer = new \Swift_Mailer($transport);
					$message = \Swift_Message::newInstance()
							->setSubject('FormArmor')
							->setFrom(array('FormArmor@gmail.com' => 'FormArmor'))
							->setTo($repInscription->retournerMailInscription($id));

					$message->setBody(
						$this->renderView(
								'FormArmorBundle:Admin:validationInscription.html.twig',
								array('message' => 'L\'équipe FormArmor vous confirme votre inscription à la session suivante', 'annuler' => 0, 'imageEntete' => $message->embed($imageEntete), 'imageCarte' => $message->embed($imageCarte), 'imageValise' => $message->embed($imageValise), 'imageType' => $message->embed($imageType), 'imageDipl' => $message->embed($imageDipl), 'imageCalendrier' => $message->embed($imageCalendrier), 'imageDuree' => $message->embed($imageDuree), 'session' => $session)
						),
					'text/html'
					);

					$mailer->send($message);
				}
				
				// Réaffichage de la liste des sessions
				$nbParPage = $this->container->getParameter('nb_par_page');
				// On récupère l'objet Paginator
				$lesSessions = $rep->listeSessions(1, $nbParPage);
				
				// On calcule le nombre total de pages grâce au count($lesSessions) qui retourne le nombre total de sessions
				$nbPages = ceil(count($lesSessions) / $nbParPage);
				
				// On donne toutes les informations nécessaires à la vue
				return $this->render('FormArmorBundle:Admin:session.html.twig', array(
				  'lesSessions' => $lesSessions,
				  'nbPages'     => $nbPages,
				  'page'        => 1,
				));
			}
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formSession.html.twig', array('form' => $form->createView(), 'action' => 'modification'));
    }
	public function suppSessionAction($id, Request $request) // Affichage du formulaire de suppression d'une session
    {
        // Récupération de la session d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Session_formation');
		$session = $rep->find($id);
		
		// Création du formulaire à partir de la session "récupérée"
		$form   = $this->get('form.factory')->create(SessionType::class, $session);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			
			// Récupération de l'identifiant de la session à supprimer
			$donneePost = $request->request->get('session');
			
			// mise à jour de la bdd
			$res = $rep->suppSession($id);
			$em->persist($session);
			$em->flush();
				
			// Réaffichage de la liste des formations
			$nbParPage = $this->container->getParameter('nb_par_page');
			// On récupère l'objet Paginator
			$lesSessions = $rep->listeSessions(1, $nbParPage);
				
			// On calcule le nombre total de pages grâce au count($lesSessions) qui retourne le nombre total de sessions
			$nbPages = ceil(count($lesSessions) / $nbParPage);
				
			// On donne toutes les informations nécessaires à la vue
			return $this->render('FormArmorBundle:Admin:session.html.twig', array(
				'lesSessions' => $lesSessions,
				'nbPages'     => $nbPages,
				'page'        => 1,
				));
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formSession.html.twig', array('form' => $form->createView(), 'action' => 'SUPPRESSION'));
	}
	public function validerSessionAction($id, Request $request) // Affichage du formulaire de validation d'une session
    {
		// Récupération de la session d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Session_formation');
		$session = $rep->find($id);

		$rep = $em->getRepository('FormArmorBundle:Inscription');
		$lesInscriptions = $rep->listeInscriptions($session);

		// Création du formulaire à partir de la session "récupérée"
		$form   = $this->get('form.factory')->create(SessionType::class, $session);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				$transport = (new \Swift_SmtpTransport($this->container->getParameter('mailer_host'), $this->container->getParameter('mailer_port'),$this->container->getParameter('mailer_encryption')))
				->setUsername($this->container->getParameter('mailer_user'))
				->setPassword($this->container->getParameter('mailer_password'));

				$mailer = new \Swift_Mailer($transport);
				$desti = array();
				$i=0;
				foreach ($lesInscriptions as $inscription)
				{
					$desti[$i] = $inscription->getClient()->getEmail();
					$i++;
				}

				//Envoie d'un email  à tous les clients inscrits
				$message =  \Swift_Message::newInstance()
				->setSubject('Hello Email')
				->setFrom(['FormArmor@gmail.com' => 'FormArmor'])
				->setTo($desti)
				->setBody(
					$this->renderView(
						'FormArmorBundle:Client:validationSession.html.twig',
						['name' => $inscription->getClient()->getNom(), 'session' => $session]
					),
					'text/html'
				);

				$mailer->send($message);
				
				$session->setClose(1);
				// mise à jour de la bdd
				$em->persist($session);
				$em->flush();
					
				// Réaffichage de la liste des formations
				$nbParPage = $this->container->getParameter('nb_par_page');
				// On récupère l'objet Paginator
				$rep = $em->getRepository('FormArmorBundle:Session_formation');
				$lesSessions = $rep->listeSessions(1, $nbParPage);
					
				// On calcule le nombre total de pages grâce au count($lesSessions) qui retourne le nombre total de sessions
				$nbPages = ceil(count($lesSessions) / $nbParPage);
					
				// On donne toutes les informations nécessaires à la vue
				return $this->render('FormArmorBundle:Admin:session.html.twig', array(
					'lesSessions' => $lesSessions,
					'nbPages'     => $nbPages,
					'page'        => 1,
					));
			}
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formSessionValidation.html.twig', array('form' => $form->createView(), 'action' => 'VALIDATION', 'UneSession' => $session, 'lesInscriptions' => $lesInscriptions));
	}	
	// Gestion des plans de formation
	public function listePlanFormationAction($page)
	{
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
		$rep = $manager->getRepository('FormArmorBundle:Plan_formation');
		$lesPlans = $rep->listePlans($page, $nbParPage);
		
		// On calcule le nombre total de pages grâce au count($lesPlans) qui retourne le nombre total de plans de formation
		$nbPages = ceil(count($lesPlans) / $nbParPage);
		
		// Si la page n'existe pas, on retourne une erreur 404
		if ($page > $nbPages)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		// On donne toutes les informations nécessaires à la vue
		return $this->render('FormArmorBundle:Admin:plan.html.twig', array(
		  'lesPlans' => $lesPlans,
		  'nbPages'     => $nbPages,
		  'page'        => $page,
		));
	}
	public function modifPlanFormationAction($id, Request $request) // Affichage du formulaire de modification d'un plan de formation
    {
        // Récupération de la formation d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Plan_formation');
		$plan = $rep->find($id);
		
		// Création du formulaire à partir du plan "récupéré"
		$form   = $this->get('form.factory')->create(PlanFormationType::class, $plan);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				// mise à jour de la bdd
				$em->persist($plan);
				$em->flush();				
				
				// Réaffichage de la liste des sessions
				$nbParPage = $this->container->getParameter('nb_par_page');
				// On récupère l'objet Paginator
				$lesPlans = $rep->listePlans(1, $nbParPage);
				
				// On calcule le nombre total de pages grâce au count($lesSessions) qui retourne le nombre total de sessions
				$nbPages = ceil(count($lesPlans) / $nbParPage);
				
				// On donne toutes les informations nécessaires à la vue
				return $this->render('FormArmorBundle:Admin:plan.html.twig', array(
				  'lesPlans' => $lesPlans,
				  'nbPages'     => $nbPages,
				  'page'        => 1,
				));
			}
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formPlan.html.twig', array('form' => $form->createView(), 'action' => 'modification'));
    }
	public function suppPlanFormationAction($id, Request $request) // Affichage du formulaire de suppression d'un plan de formation
    {
        // Récupération du plan de formation d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Plan_formation');
		$plan = $rep->find($id);
		
		// Création du formulaire à partir du plan de formation "récupéré"
		$form   = $this->get('form.factory')->create(PlanFormationType::class, $plan);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			
			// mise à jour de la bdd
			$res = $rep->suppPlanFormation($id);
			$em->persist($plan);
			$em->flush();
				
			// Réaffichage de la liste des plans de formation
			$nbParPage = $this->container->getParameter('nb_par_page');
			// On récupère l'objet Paginator
			$lesPlans = $rep->listePlans(1, $nbParPage);
				
			// On calcule le nombre total de pages grâce au count($lesPlans) qui retourne le nombre total de plans de formation
			$nbPages = ceil(count($lesPlans) / $nbParPage);
				
			// On donne toutes les informations nécessaires à la vue
			return $this->render('FormArmorBundle:Admin:plan.html.twig', array(
				'lesPlans' => $lesPlans,
				'nbPages'     => $nbPages,
				'page'        => 1,
				));
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formPlan.html.twig', array('form' => $form->createView(), 'action' => 'SUPPRESSION'));
		}
		
		
		//ANGELIQUE §§§§§§ 
		

		public function inscriptionsProvisoiresAction($id)
		{
		// On récupère l'objet Paginator
		$manager = $this->getDoctrine()->getManager();
		$repInscription = $manager->getRepository('FormArmorBundle:Inscription');
		$lesInscriptions = $repInscription->retournerInscription($id);

		$repSession = $manager->getRepository('FormArmorBundle:Session_formation');
		$session = $repSession->find($id);
		$nbHeures = $session->getFormation()->getDuree(); 
		$sommeTauxHoraire = 0;
		foreach($lesInscriptions as $inscription)
		{
			$sommeTauxHoraire += $inscription->getClient()->getStatut()->getTauxHoraire() * $nbHeures;
		}
		$marge = $sommeTauxHoraire - $session->getFormation()->getCoutrevient()  * $nbHeures ;
		
		// On donne toutes les informations nécessaires à la vue
		return $this->render('FormArmorBundle:Admin:inscriptions.html.twig', array(
		  'lesInscriptions' => $lesInscriptions,
		  'marge' => $marge,
		  'formation' => $session->getFormation(),
			'sessionId' => $id,
			'session' => $session
		));
	}

	public function validationInscriptionsAction($id)
	{
		$transport = (new \Swift_SmtpTransport($this->container->getParameter('mailer_host'), $this->container->getParameter('mailer_port'), $this->container->getParameter('mailer_encryption')))
		->setUsername($this->container->getParameter('mailer_user'))
		->setPassword($this->container->getPArameter('mailer_password'));
	
		$manager = $this->getDoctrine()->getManager();
		$repInscription = $manager->getRepository('FormArmorBundle:Inscription');

		$repSession =  $manager->getRepository('FormArmorBundle:Session_formation');

		$session = $repSession->find($id);

		$imageEntete = \Swift_Image::fromPath('../web/images/banniere.jpg');
		$imageCarte = \Swift_Image::fromPath('../web/images/carte_Formarmor.jpg');
		$imageValise = \Swift_Image::fromPath('../web/images/valise.jpg');
		if($session->getFormation()->getTypeForm() == "Compta"){
			$imageType = \Swift_Image::fromPath('../web/images/compta.jpg');
		}else{
			$imageType = \Swift_Image::fromPath('../web/images/bur.jpg');
		}
		if($session->getFormation()->getDiplomante() == 1){
			$imageDipl = \Swift_Image::fromPath('../web/images/dipl.jpg');
		}else{
			$imageDipl = \Swift_Image::fromPath('../web/images/nonDipl.jpg');
		}
		$imageCalendrier = \Swift_Image::fromPath('../web/images/calendrier.jpg');
		$imageDuree = \Swift_Image::fromPath('../web/images/duree.jpg');

		$mailer = new \Swift_Mailer($transport);
		$message = \Swift_Message::newInstance()
        ->setSubject('FormArmor')
        ->setFrom(array('FormArmor@gmail.com' => 'FormArmor'))
        ->setTo($repInscription->retournerMailInscription($id));

		$message->setBody(
			$this->renderView(
					'FormArmorBundle:Admin:validationInscription.html.twig',
					array('message' => 'L\'équipe FormArmor vous confirme votre inscription à la session suivante', 'annuler' => 0, 'imageEntete' => $message->embed($imageEntete), 'imageCarte' => $message->embed($imageCarte), 'imageValise' => $message->embed($imageValise), 'imageType' => $message->embed($imageType), 'imageDipl' => $message->embed($imageDipl), 'imageCalendrier' => $message->embed($imageCalendrier), 'imageDuree' => $message->embed($imageDuree), 'session' => $session)
			),
		 'text/html'
		);

		$mailer->send($message);

		$manager = $this->getDoctrine()->getManager();
		$repSession = $manager->getRepository('FormArmorBundle:Session_formation');
		$session = $repSession->find($id);
		$session->setClose(1);
		$manager->persist($session);
		$manager->flush();

		return $this->listeSessionAction(1);
	}

	public function suppressionInscriptionsAction($id, Request $request)
	{
		$motifAnnulation = $request->get('motifAnnulation');

		$transport = (new \Swift_SmtpTransport($this->container->getParameter('mailer_host'), $this->container->getParameter('mailer_port'), $this->container->getParameter('mailer_encryption')))
		->setUsername($this->container->getParameter('mailer_user'))
		->setPassword($this->container->getPArameter('mailer_password'));
	
		$manager = $this->getDoctrine()->getManager();
		$repInscription = $manager->getRepository('FormArmorBundle:Inscription');

		$repSession =  $manager->getRepository('FormArmorBundle:Session_formation');

		$session = $repSession->find($id);

		$imageEntete = \Swift_Image::fromPath('../web/images/banniere.jpg');
		$imageCarte = \Swift_Image::fromPath('../web/images/carte_Formarmor.jpg');
		$imageValise = \Swift_Image::fromPath('../web/images/valise.jpg');
		if($session->getFormation()->getTypeForm() == "Compta"){
			$imageType = \Swift_Image::fromPath('../web/images/compta.jpg');
		}else{
			$imageType = \Swift_Image::fromPath('../web/images/bur.jpg');
		}
		if($session->getFormation()->getDiplomante() == 1){
			$imageDipl = \Swift_Image::fromPath('../web/images/dipl.jpg');
		}else{
			$imageDipl = \Swift_Image::fromPath('../web/images/nonDipl.jpg');
		}
		$imageCalendrier = \Swift_Image::fromPath('../web/images/calendrier.jpg');
		$imageDuree = \Swift_Image::fromPath('../web/images/duree.jpg');

		$mailer = new \Swift_Mailer($transport);
		$message = \Swift_Message::newInstance()
        ->setSubject('FormArmor')
        ->setFrom(array('FormArmor@gmail.com' => 'FormArmor'))
        ->setTo($repInscription->retournerMailInscription($id));

		$message->setBody(
			$this->renderView(
					'FormArmorBundle:Admin:validationInscription.html.twig',
					array('message' => $motifAnnulation, 'annuler' => 0, 'imageEntete' => $message->embed($imageEntete), 'imageCarte' => $message->embed($imageCarte), 'imageValise' => $message->embed($imageValise), 'imageType' => $message->embed($imageType), 'imageDipl' => $message->embed($imageDipl), 'imageCalendrier' => $message->embed($imageCalendrier), 'imageDuree' => $message->embed($imageDuree), 'session' => $session)
			),
		 'text/html'
		);

		$mailer->send($message);

		$emInscription = $this->getDoctrine()->getManager();
		$repInscription = $emInscription->getRepository('FormArmorBundle:Inscription');
		$lesInscriptions = $repInscription->retournerInscriptions();
		foreach($lesInscriptions as $uneInscription){
			if($uneInscription->getSessionFormation()->getId() == $id){
				$repInscription = $repInscription->suppInscription($uneInscription->getId());
				$emInscription->persist($uneInscription);
				$emInscription->flush();
			}			
		}
		$emSession = $this->getDoctrine()->getManager();
		$repSession = $emSession->getRepository('FormArmorBundle:Session_formation');
		$repSession = $repSession->suppSession($id);
		$emSession->flush();

		return $this->listeSessionAction(1);
	}

	public function infoClientAction($idClient, $idFormation){
		$emClient = $this->getDoctrine()->getManager();
		$repClient = $emClient->getRepository('FormArmorBundle:Client');
		$client = $repClient->find($idClient);

		$emFormation = $this->getDoctrine()->getManager();
		$repFormation = $emFormation->getRepository('FormArmorBundle:Formation');
		$formation = $repFormation->find($idFormation);
	
		$emInscription = $this->getDoctrine()->getManager();
		$repInscription = $emInscription->getRepository('FormArmorBundle:Inscription');
		$inscription = $repInscription->retourneNbHClient($idClient);

		if($formation->getTypeForm() == 'Compta'){
			$totCompta = $formation->getDuree() + $inscription[0];
			$totBureau = $inscription[1];
		}else{
			$totCompta = $inscription[0];
			$totBureau = $formation->getDuree() + $inscription[1];
		}

		if($totCompta > $client->getNbhcpta()){
			$colorCompta = 'red';
		}else{
			$colorCompta = 'green';
		}
	
		if($totBureau > $client->getNbhbur()){
			$colorBureau = 'red';
		}else{
			$colorBureau = 'green';
		}
		return new Response($client->getNom().'/'.$client->getNbhcpta().'/'.$client->getNbhbur().'/'.$formation->getTypeForm().'/'.$formation->getDuree().'/'.$totCompta.'/'.$totBureau.'/'.$colorCompta.'/'.$colorBureau);
	}
}
