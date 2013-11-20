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
    private $children;
	
	/**
     * @ORM\ManyToOne(targetEntity="Rap2h\CookingBundle\Entity\RecipeItem", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    private $parent;
	
	public function __construct() {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }
	
	public function addChild(RecipeItem $item) {
		$item->setParent($this);
		$this->children->add($item);
	}
	
	public function setParent(RecipeItem $item) {
		$this->parent = $item;
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
}
