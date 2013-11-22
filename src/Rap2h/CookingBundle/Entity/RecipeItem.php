<?php

namespace Rap2h\CookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rap2h\CookingBundle\Entity\RecipeItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Rap2h\CookingBundle\Entity\RecipeItemRepository")
 */
class RecipeItem
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
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;

	/**
     * @ORM\OneToMany(targetEntity="Rap2h\CookingBundle\Entity\RecipeItem", mappedBy="parent", cascade={"persist", "remove"})
     **/
    private $childs;

	/**
     * @ORM\ManyToOne(targetEntity="Rap2h\CookingBundle\Entity\RecipeItem", inversedBy="childs")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    private $parent;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->childs = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set text
     *
     * @param string $text
     * @return RecipeItem
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Add child
     *
     * @param \Rap2h\CookingBundle\Entity\RecipeItem $child
     * @return RecipeItem
     */
    public function addChild(\Rap2h\CookingBundle\Entity\RecipeItem $child)
    {
        $this->childs[] = $child;
    	$child->setParent($this);
        return $this;
    }

    /**
     * Remove child
     *
     * @param \Rap2h\CookingBundle\Entity\RecipeItem $child
     */
    public function removeChild(\Rap2h\CookingBundle\Entity\RecipeItem $child)
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
     * @param \Rap2h\CookingBundle\Entity\RecipeItem $parent
     * @return RecipeItem
     */
    public function setParent(\Rap2h\CookingBundle\Entity\RecipeItem $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Rap2h\CookingBundle\Entity\RecipeItem
     */
    public function getParent()
    {
        return $this->parent;
    }
}