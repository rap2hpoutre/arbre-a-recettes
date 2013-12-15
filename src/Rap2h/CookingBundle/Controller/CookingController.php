<?php

namespace Rap2h\CookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Rap2h\CookingBundle\Entity\Recipe;
use Rap2h\CookingBundle\Entity\RecipeStep;
use Rap2h\CookingBundle\Entity\RecipeStepText;

use Rap2h\CookingBundle\Form\RecipeType;

/**
 * CookingController
 *
 * @package arbre a recettes
 * @author Rap2h
 * @access public
 */
class CookingController extends Controller {

	/**
	 * CookingController::indexAction()
	 *
	 * @return
	 */
	public function indexAction() {
		return $this->render('Rap2hCookingBundle:Cooking:index.html.twig');
	}

	/**
	 * CookingController::aboutAction()
	 *
	 * @return
	 */
	public function aboutAction() {
		return $this->render('Rap2hCookingBundle:Cooking:about.html.twig');
	}

	/**
	 * CookingController::startAction()
	 *
	 * @return
	 */
	public function startAction() {
		// Chargement des éléments de la recette en train d'être faite
		$items = $this->get('rap2h_cooking.cooking_session')->getItems();

		// Chargement des items disponibles
		$repository = $this->getDoctrine()->getManager()->getRepository('Rap2hCookingBundle:RecipeStep');
		$availableRecipeSteps = $repository->findBy(array('parent' => count($items) ? end($items)->id : null));

		// Rendu
		return $this->render(
			'Rap2hCookingBundle:Cooking:start.html.twig', 
			array(
				"recipeSteps" => $items, 
				'availableRecipeSteps' => $availableRecipeSteps
			)
		);
	}

	/**
	 * CookingController::resetRecipeAction()
	 *
	 * @return
	 */
	public function resetRecipeAction() {
		$this->get('rap2h_cooking.cooking_session')->clearItems();
		return $this->redirect($this->generateUrl('CookingStart'));
	}

	/**
	 * CookingController::addRecipeStepAction()
	 *
	 * @param mixed $recipeItemId
	 * @return
	 */
	public function addRecipeStepAction($recipeItemId) {

		$item = $this->getDoctrine()->getManager()->getRepository('Rap2hCookingBundle:RecipeStep')->find($recipeItemId);

		$this->get('rap2h_cooking.cooking_session')->addItem($item->getRandomText(), $item->getId());

		return $this->redirect($this->generateUrl('CookingStart'));

	}
}