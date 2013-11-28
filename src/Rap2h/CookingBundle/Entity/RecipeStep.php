<?php

namespace Rap2h\CookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RecipeStep
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Rap2h\CookingBundle\Entity\RecipeStepRepository")
 */
class RecipeStep
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
     * @ORM\ManyToMany(targetEntity="Rap2h\CookingBundle\Entity\RecipeStepText", cascade={"persist"})
     */
    private $texts;

    /**
     * @ORM\OneToMany(targetEntity="Rap2h\CookingBundle\Entity\RecipeStep", mappedBy="parent", cascade={"persist"})
     **/
    private $childs;

    /**
     * @ORM\ManyToOne(targetEntity="Rap2h\CookingBundle\Entity\RecipeStep", inversedBy="childs")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    private $parent;


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
     * Constructor
     */
    public function __construct()
    {
        $this->childs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add child
     *
     * @param \Rap2h\CookingBundle\Entity\RecipeStep $child
     * @return RecipeStep
     */
    public function addChild(\Rap2h\CookingBundle\Entity\RecipeStep $child)
    {
        $this->childs[] = $child;
        $child->setParent($this);
        return $this;
    }

    /**
     * Remove child
     *
     * @param \Rap2h\CookingBundle\Entity\RecipeStep $child
     */
    public function removeChild(\Rap2h\CookingBundle\Entity\RecipeStep $child)
    {
        $this->childs->removeElement($child);
        $child->setParent(null);
    }

    /**
     * Get childs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Set parent
     *
     * @param \Rap2h\CookingBundle\Entity\RecipeStep $parent
     * @return RecipeStep
     */
    public function setParent(\Rap2h\CookingBundle\Entity\RecipeStep $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Rap2h\CookingBundle\Entity\RecipeStep
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add texts
     *
     * @param \Rap2h\CookingBundle\Entity\RecipeStepText $text
     * @return RecipeStep
     */
    public function addText(\Rap2h\CookingBundle\Entity\RecipeStepText $text)
    {
        $this->texts[] = $text;

        return $this;
    }

    /**
     * Remove texts
     *
     * @param \Rap2h\CookingBundle\Entity\RecipeStepText $text
     */
    public function removeText(\Rap2h\CookingBundle\Entity\RecipeStepText $text)
    {
        $this->texts->removeElement($text);
    }

    /**
     * Get texts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTexts()
    {
        return $this->texts;
    }

    public function setTexts($texts) {
		$this->texts = $texts;
   	}

    public function getRandomText() {
        return $this->texts[mt_rand(0, count($this->texts) - 1)]->getText();
    }
}