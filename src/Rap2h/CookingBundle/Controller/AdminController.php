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
 * AdminController
 *
 * @package arbre a recettes
 * @author e-doceo
 * @copyright 2013
 * @version $Id$
 * @access public
 */
class AdminController extends Controller {

	/**
	 * Ajout d'une recette
	 */
	public function addRecipeAction() {

		// Création de l'entité
		$recipe = new Recipe();

		// Formulaire
		$form = $this->createForm(new RecipeType(), $recipe);

		// Traitement des requetes
		$request = $this->get('request');

		// Validation du formulaire
		if ($request->getMethod() == 'POST') {

			$form->bind($request);

			// Traitement du formulaire s'il est valide
			if ($form->isValid()) {

				// La partie textarea qu'il faut parser séparément ...
				$unlinked_recipe_text = $form->get('unlinked_recipe_text')->getData();

				// ... pour enregistrer les étapes
				$steps = explode( "\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $unlinked_recipe_text ) ) );
				foreach ($steps as $key => $step) {

					$recipeStepText = new RecipeStepText();
					$recipeStepText->setText(trim($step));

					if (isset($recipeStep)) $lastRecipeStep = $recipeStep;

					$recipeStep = new RecipeStep();
					$recipeStep->addText($recipeStepText);
					if (isset($lastRecipeStep)) $recipeStep->setParent($lastRecipeStep);

					$recipe->addRecipeStep($recipeStep);
				}

				// On utilisera le manager ...
				$em = $this->getDoctrine()->getManager();
				// ... pour persister ...
				$em->persist($recipe);
				// ... puis enregistrer
				$em->flush();

				// Information flash de l'enregistrement
				$this->get('session')->getFlashBag()->add('info', 'recette par formulaire ajouté');

				// Redirection index (pour l'instant)
				return $this->redirect($this->generateUrl('CookingIndex'));
			}
		}

		return $this->render('Rap2hCookingBundle:Cooking:addRecipe.html.twig', array(
			'form' => $form->createView(),
		));
	}



	/**
	 * Associer des étapes entre elles
	 *
	 * @param mixed $request
	 * @return
	 */
	public function joinRecipeStepsAction(Request $request) {
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
			    'label' => 'Etape n°1',
			    'attr' => array('class' => 'form-control')
			))
    		->add('right', 'entity', array(
			    'class' => 'Rap2hCookingBundle:RecipeStep',
			    'query_builder' => function($repository) {
			    	return $repository->createQueryBuilder('p')->orderBy('p.id', 'ASC') ;
			   	},
			    'property' => 'randomText',
			    'label' => 'Etape n°2',
			    'attr' => array('class' => 'form-control')
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

	/**
	 * AdminController::manageRecipesAction()
	 *
	 * @return
	 */
	public function manageRecipesAction() {
		$recipes = $this->getDoctrine()->getManager()->getRepository('Rap2hCookingBundle:Recipe')->findAll();

		return $this->render('Rap2hCookingBundle:Cooking:manageRecipes.html.twig', array(
			'recipes' => $recipes,
		));
	}

	/**
	 * AdminController::deleteRecipeAction()
	 *
	 * @param mixed $recipeId
	 * @todo mettre un CSRF
	 * @todo l'ordre de traitement déconne au niveau des steps
	 * @return
	 */
	public function deleteRecipeAction($recipeId) {
		// On a besoin du manager
		$em = $this->getDoctrine()->getManager();

		// On charge la recette
		$recipe = $em->getRepository('Rap2hCookingBundle:Recipe')->find($recipeId);

		// On va supprimer tous ses steps qui deviendraient orphelin
		// On fait ici une suppression en cascade "intelligente", il n'y a pas moyen de passer par les annotations ...
		// .. c'est pas faute d'avoir cherché (je suis dans un cas spécial)
		foreach($recipe->getRecipeSteps() as $step) {
			// S'il ce n'est associé qu'à une recette c'est celle là, donc on va pouvoir le supprimer
			if (count($step->getRecipes()) <= 1) {
				// Mais avant on va aussi supprimer les textes potentiellement plus utilisés
				foreach($step->getTexts() as $text) {
					if (count($text->getSteps()) <= 1) {
						$em->remove($text);
				 	}
				}
				$step->setParent(null);
				foreach($step->getChilds() as $child) {
					$step->removeChild($child);
				}
				// $em->persist($step);
				$em->remove($step);
			}
		}

		$em->remove($recipe);
		$em->flush();

		$this->get('session')->getFlashBag()->add('info', 'La recette vient d\'être supprimée');

		return $this->redirect($this->generateUrl('CookingAdminManageRecipes'));
	}

	/**
	 * AdminController::cleanDatabase()
	 *
	 * @return
	 */
	public function cleanDatabase() {
		// TODO
	}
}

