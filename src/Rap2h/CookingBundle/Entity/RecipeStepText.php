<?php

namespace Rap2h\CookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RecipeStepText
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Rap2h\CookingBundle\Entity\RecipeStepTextRepository")
 */
class RecipeStepText {
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
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;

    /**
  	 * @ORM\ManyToMany(targetEntity="Rap2h\CookingBundle\Entity\RecipeStep", mappedBy="texts")
     */
    private $steps;

    /**
     * Constructor
     */
    public function __construct() {
        $this->steps = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return RecipeStepText
     */
    public function setText($text) {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText() {
        return $this->text;
    }


    /**
     * Add step
     *
     * @param \Rap2h\CookingBundle\Entity\RecipeStep $step
     * @return RecipeStepText
     */
    public function addStep(\Rap2h\CookingBundle\Entity\RecipeStep $step) {
        $this->steps[] = $step;

        return $this;
    }

    /**
     * Remove step
     *
     * @param \Rap2h\CookingBundle\Entity\RecipeStep $step
     */
    public function removeStep(\Rap2h\CookingBundle\Entity\RecipeStep $step) {
        $this->steps->removeElement($step);
    }

    /**
     * Get steps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSteps() {
        return $this->steps;
    }
}