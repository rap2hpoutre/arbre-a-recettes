<?php

namespace Rap2h\CookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecipeType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label' => 'Titre de la recette'))
            ->add('author', 'text', array('label' => 'Nom de l\'auteur'))
            ->add('recipeItems', 'collection', array(
    			'type' => new RecipeItemType(),
			    'allow_add' => true,
			    'label' => 'Liste des étapes pour réaliser la recette'
			))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rap2h\CookingBundle\Entity\Recipe'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rap2h_cookingbundle_recipe';
    }
}
