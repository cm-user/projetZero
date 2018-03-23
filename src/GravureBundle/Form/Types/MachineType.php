<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 15/03/2018
 * Time: 11:36
 */

namespace GravureBundle\Form\Types;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MachineType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,[
                'label' => 'Nom de la machine'
            ])
            ->add('type', ChoiceType::class, array(
                'choices' => array('pdf' => 'pdf', 'mail' => 'mail'),
                'expanded' => true,
                'multiple' => false
            ))
        ;
    }


}