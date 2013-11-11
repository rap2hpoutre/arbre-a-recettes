<?php
 
namespace Rap2h\CookingBundle\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
 
class CookingController extends Controller
{
  public function indexAction()
  {
  	$session = new Session();
	$session->start();
  	$session->set('recipeItems', null);
    return $this->render('Rap2hCookingBundle:Cooking:index.html.twig', array("recipeItems" => null));
  }

  public function addRecipeItemAction($recipeItemId) {
	$session = new Session();
	$session->start();
	$items = $session->get('recipeItems');
	$items[] = $recipeItemId;
	$session->set('recipeItems', $items);
	return $this->render('Rap2hCookingBundle:Cooking:index.html.twig', array("recipeItems" => $items));
  }
}