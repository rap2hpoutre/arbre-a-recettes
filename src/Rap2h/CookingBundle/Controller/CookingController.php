<?php

namespace Rap2h\CookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class CookingController extends Controller {

	public function indexAction() {
		$session = $this->get('session');
		$session->start();
		$items = $session->get('recipeItems');
		return $this->render('Rap2hCookingBundle:Cooking:index.html.twig', array("recipeItems" => $items));
	}

	public function resetRecipeAction() {
		$session = $this->get('session');
		$session->start();
		$session->set('recipeItems', null);
		return $this->redirect($this->generateUrl('CookingIndex'));
	}

	public function addRecipeItemAction($recipeItemId) {

		$session = $this->get('session');
		$session->start();
		$items = $session->get('recipeItems');
		$items[] = $recipeItemId;
		$session->set('recipeItems', $items);

		$session->getFlashBag()->add('info', 'Tu viens d\'ajouter une ' . $recipeItemId);

		return $this->redirect($this->generateUrl('CookingIndex'));

	}
}