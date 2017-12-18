<?php

namespace CM\ServiceClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class SolutionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',     TextType::class)
            ->add('textSolution',   CKEditorType::class, array(
                'label'             => 'Description de la solution',
                'config' => array(
                    'language'    => 'fr'
                ),
            ))
            ->add('mailBool')
            ->add('branche', EntityType::class, array(
                'class'        => 'CM\ServiceClientBundle\Entity\Branch',
                'choice_label' => 'nom',
                'multiple' => false,
                'required' => false,
            ));
            
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CM\ServiceClientBundle\Entity\Solution'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cm_serviceclientbundle_solution';
    }


}
