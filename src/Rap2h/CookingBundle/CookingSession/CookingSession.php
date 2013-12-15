<?php
namespace Rap2h\CookingBundle\CookingSession;

use Symfony\Component\HttpFoundation\Session\Session;
/**
 * CookingSession
 *
 * Pour gérer la recette que l'utilisateur est en train de choisir (la session de cuisine)
 */
class CookingSession {

	protected $session;
	protected $items;

	public function __construct(Session $session) {
		$this->session = $session;
		$this->session->start();
		$this->getItems();
	}

	public function getItems() {
		$this->items = $this->session->get('recipeSteps');
		return $this->items;
	}

	public function addItem($text, $id) {
		$this->items[] = (object)array('text' => $text, 'id' => $id);
		$this->session->set('recipeSteps', $this->items);
	}

	public function clearItems() {
		$this->items = null;
		$this->session->set('recipeSteps', null);
	}
}