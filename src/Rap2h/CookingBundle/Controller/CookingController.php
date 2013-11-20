<?php

namespace Rap2h\CookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Rap2h\CookingBundle\Entity\RecipeItem;

class CookingController extends Controller {

	public function indexAction() {
		return $this->render('Rap2hCookingBundle:Cooking:index.html.twig');
	}

	public function aboutAction() {
		return $this->render('Rap2hCookingBundle:Cooking:about.html.twig');
	}

	public function startAction() {
		$session = $this->get('session');
		$session->start();
		$items = $session->get('recipeItems');
		
		$repository = $this->getDoctrine()->getManager()->getRepository('Rap2hCookingBundle:RecipeItem');
		$availableRecipeItems = $repository->findBy(array('parent' => count($items) ? end($items)->id : null));
		
		return $this->render('Rap2hCookingBundle:Cooking:start.html.twig', array("recipeItems" => $items, 'availableRecipeItems' => $availableRecipeItems));
	}

	public function resetRecipeAction() {
		$session = $this->get('session');
		$session->start();
		$session->set('recipeItems', null);
		return $this->redirect($this->generateUrl('CookingStart'));
	}

	public function addRecipeItemAction($recipeItemId) {

		$session = $this->get('session');
		$session->start();
		
		$item = $this->getDoctrine()->getManager()->getRepository('Rap2hCookingBundle:RecipeItem')->find($recipeItemId);
		

		$session->getFlashBag()->add('info', 'Tu viens d\'ajouter une ' . $item->getText());
		$itema = new \stdClass();
		$itema->text = $item->getText();
		$itema->id = $item->getId();
		
		$items = $session->get('recipeItems');
		$items[] = $itema;
		$session->set('recipeItems', $items);

		return $this->redirect($this->generateUrl('CookingStart'));

	}
	
	
	public function adminAddRandomItemAction($addChilds) {
		$recipeItem = new RecipeItem();
		$recipeItem->setText(uniqid());
		
		if ($addChilds) {
			for($i =0; $i < rand(1,5); $i++) {
				$recipeItemChild = new RecipeItem();
				$recipeItemChild->setText(uniqid('child_'));
				$recipeItem->addChild($recipeItemChild);
			}
		}
		
		$em = $this->getDoctrine()->getManager();
		
		$this->get('session')->getFlashBag()->add('info', 'élément de recette aléatoire ajouté');
		
		$em->persist($recipeItem);
		$em->flush();
		
		return $this->redirect($this->generateUrl('CookingIndex'));
	}
	
	public function adminAddRecipeAction() {
		$recipeItem = new RecipeItem();
		$recipeItem ->setParent($this->getDoctrine()->getManager()->getRepository('Rap2hCookingBundle:RecipeItem')->find(2));
		$formBuilder = $this->createFormBuilder($recipeItem);
		$formBuilder->add('text', 'text');
		$form = $formBuilder->getForm();

		// On récupère la requête
		$request = $this->get('request');

		// On vérifie qu'elle est de type POST
		if ($request->getMethod() == 'POST') {
			// On fait le lien Requête <-> Formulaire
			// À partir de maintenant, la variable $article contient les valeurs entrées dans le formulaire par le visiteur
			$form->bind($request);

			// On vérifie que les valeurs entrées sont correctes
			// (Nous verrons la validation des objets en détail dans le prochain chapitre)
			if ($form->isValid()) {
				// On l'enregistre notre objet $article dans la base de données
				$em = $this->getDoctrine()->getManager();
				$em->persist($recipeItem);
				$em->flush();
				
				$this->get('session')->getFlashBag()->add('info', 'élément de recette par formulaire ajouté');
				
				return $this->redirect($this->generateUrl('CookingIndex'));
				
			}
		}




		return $this->render('Rap2hCookingBundle:Cooking:addRecipe.html.twig', array(
			'form' => $form->createView(),
		));
	}
	
}