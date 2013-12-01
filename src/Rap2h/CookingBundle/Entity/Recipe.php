<?php

namespace Rap2h\CookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recipe
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Rap2h\CookingBundle\Entity\RecipeRepository")
 */
class Recipe
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
  	 * @ORM\ManyToMany(targetEntity="Rap2h\CookingBundle\Entity\RecipeStep", cascade={"persist", "remove"})
     */
    private $recipeSteps;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Recipe
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return Recipe
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->recipeSteps = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add recipeSteps
     *
     * @param \Rap2h\CookingBundle\Entity\RecipeStep $recipeSteps
     * @return Recipe
     */
    public function addRecipeStep(\Rap2h\CookingBundle\Entity\RecipeStep $recipeStep)
    {
        $this->recipeSteps[] = $recipeStep;

        return $this;
    }

    /**
     * Remove recipeSteps
     *
     * @param \Rap2h\CookingBundle\Entity\RecipeStep $recipeSteps
     */
    public function removeRecipeStep(\Rap2h\CookingBundle\Entity\RecipeStep $recipeStep)
    {
        $this->recipeSteps->removeElement($recipeStep);
    }

    /**
     * Get recipeSteps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecipeSteps()
    {
        return $this->recipeSteps;
    }
}