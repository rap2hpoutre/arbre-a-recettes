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
		$items = $session->get('recipeSteps');

		$repository = $this->getDoctrine()->getManager()->getRepository('Rap2hCookingBundle:RecipeStep');
		$availableRecipeSteps = $repository->findBy(array('parent' => count($items) ? end($items)->id : null));

		return $this->render('Rap2hCookingBundle:Cooking:start.html.twig', array("recipeSteps" => $items, 'availableRecipeSteps' => $availableRecipeSteps));
	}

	public function resetRecipeAction() {
		$session = $this->get('session');
		$session->start();
		$session->set('recipeSteps', null);
		return $this->redirect($this->generateUrl('CookingStart'));
	}

	public function addRecipeStepAction($recipeItemId) {

		$session = $this->get('session');
		$session->start();

		$item = $this->getDoctrine()->getManager()->getRepository('Rap2hCookingBundle:RecipeStep')->find($recipeItemId);


		$session->getFlashBag()->add('info', 'Tu viens d\'ajouter une ' . $item->getRandomText());
		$itema = new \stdClass();
		$itema->text = $item->getRandomText();
		$itema->id = $item->getId();

		$items = $session->get('recipeSteps');
		$items[] = $itema;
		$session->set('recipeSteps', $items);

		return $this->redirect($this->generateUrl('CookingStart'));

	}

	public function adminAddRecipeAction() {
		$recipe = new Recipe();
		$form = $this->createForm(new RecipeType(), $recipe);

		$request = $this->get('request');

		if ($request->getMethod() == 'POST') {

			$form->bind($request);

			if ($form->isValid()) {

				$a = $form->getData();
				$unlinked_recipe_text = $form->get('unlinked_recipe_text')->getData();

				$em = $this->getDoctrine()->getManager();


				// Les etapes

				$steps     = explode( "\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $unlinked_recipe_text ) ) );
				foreach ($steps as $key => $step) {

					$recipeStepText = new RecipeStepText();
					$recipeStepText->setText(trim($step));

					if (isset($recipeStep)) $lastRecipeStep = $recipeStep;

					$recipeStep = new RecipeStep();
					$recipeStep->addText($recipeStepText);
					if (isset($lastRecipeStep)) $recipeStep->setParent($lastRecipeStep);

					$recipe->addRecipeStep($recipeStep);
				}
				$em->persist($recipe);
				$em->flush();

				$this->get('session')->getFlashBag()->add('info', 'recette par formulaire ajouté');

				return $this->redirect($this->generateUrl('CookingIndex'));
			}
		}

		return $this->render('Rap2hCookingBundle:Cooking:addRecipe.html.twig', array(
			'form' => $form->createView(),
		));
	}



	public function adminJoinRecipeStepsAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('Rap2hCookingBundle:RecipeStep');
		$defaultData = array('message' => 'Type your message here');
    	$form = $this->createFormBuilder($defaultData)
    		->add('left', 'entity', array(
			    'class' => 'Rap2hCookingBundle:RecipeStep',
			    'query_builder' => function($repository) {
			    	return $repository->createQueryBuilder('p')->orderBy('p.id', 'ASC') ;
			   	},
			    'property' => 'randomText',
			    'label' => 'Etape n°2'
			))
    		->add('right', 'entity', array(
			    'class' => 'Rap2hCookingBundle:RecipeStep',
			    'query_builder' => function($repository) {
			    	return $repository->createQueryBuilder('p')->orderBy('p.id', 'ASC') ;
			   	},
			    'property' => 'randomText',
			    'label' => 'Etape n°2'
			))
			->getForm()
		;

		 $form->handleRequest($request);

	    if ($form->isValid()) {
	    	$data = $form->getData();

			// Mise à jour des textes
	    	$left = $data['left'];
	    	$right = $data['right'];

	    	$all_texts = array();

	    	foreach($left->getTexts() as $text) {
				$all_texts[] = $text;
    		}

    		foreach($right->getTexts() as $text) {
				$all_texts[] = $text;
    		}

			$right->setTexts($all_texts);
			$left->setTexts($all_texts);

			$em->persist($left);

			$left_parent = $left->getParent();
   			$right_parent = $right->getParent();

			// Association au nouveau parents si le parent des deux est commun
			if ( ($left_parent == null && $right_parent == null) || ($left_parent != null && $right_parent != null && $left_parent->getId() == $right_parent->getId()) ) {
				// Charger tous ceux qui ont le 2eme pour parent
				$childs = $repository->findBy(array('parent' => $right->getId()));
				// Les mettre à jour avec le nouveau parent (le 1)
				foreach($childs as $child) {
					$child->setParent($left);
					$em->persist($child);
				}
				// Supprimer le 2eme
				$em->remove($right);
			} else {
				$em->persist($right);
			}

			$em->flush();
	    }

	    return $this->render('Rap2hCookingBundle:Cooking:joinRecipeSteps.html.twig', array(
			'form' => $form->createView(),
		));
	}

}