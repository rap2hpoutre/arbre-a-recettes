<?php

namespace Rap2h\CookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecipeItemType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'text', array('label' => 'Etape'))
            ->add('parent', 'hidden')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rap2h\CookingBundle\Entity\RecipeItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rap2h_cookingbundle_recipeitem';
    }
}
