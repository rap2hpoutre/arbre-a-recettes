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
 * @author e-doceo
 * @copyright 2013
 * @version $Id$
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
		$session = $this->get('session');
		$session->start();
		$items = $session->get('recipeSteps');

		$repository = $this->getDoctrine()->getManager()->getRepository('Rap2hCookingBundle:RecipeStep');
		$availableRecipeSteps = $repository->findBy(array('parent' => count($items) ? end($items)->id : null));

		return $this->render('Rap2hCookingBundle:Cooking:start.html.twig', array("recipeSteps" => $items, 'availableRecipeSteps' => $availableRecipeSteps));
	}

	/**
	 * CookingController::resetRecipeAction()
	 *
	 * @return
	 */
	public function resetRecipeAction() {
		$session = $this->get('session');
		$session->start();
		$session->set('recipeSteps', null);
		return $this->redirect($this->generateUrl('CookingStart'));
	}

	/**
	 * CookingController::addRecipeStepAction()
	 *
	 * @param mixed $recipeItemId
	 * @return
	 */
	public function addRecipeStepAction($recipeItemId) {

		$session = $this->get('session');
		$session->start();

		$item = $this->getDoctrine()->getManager()->getRepository('Rap2hCookingBundle:RecipeStep')->find($recipeItemId);


		// $session->getFlashBag()->add('info', 'Tu viens d\'ajouter une ' . $item->getRandomText());
		$itema = new \stdClass();
		$itema->text = $item->getRandomText();
		$itema->id = $item->getId();

		$items = $session->get('recipeSteps');
		$items[] = $itema;
		$session->set('recipeSteps', $items);

		return $this->redirect($this->generateUrl('CookingStart'));

	}
}