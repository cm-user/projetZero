<?php
//
//namespace CM\EngravingBundle\Form;
//
//use Symfony\Component\Form\AbstractType;
//use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Symfony\Component\Form\Extension\Core\Type\IntegerType;
//use Symfony\Component\Form\Extension\Core\Type\CollectionType;
//use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\OptionsResolver\OptionsResolver;
//use Symfony\Component\Form\FormEvent;
//use Symfony\Component\Form\FormEvents;
//
//class CategoryType extends AbstractType
//{
//    /**
//     * {@inheritdoc}
//     */
//    public function buildForm(FormBuilderInterface $builder, array $options)
//    {
//
//        $builder
//            ->add('idProducts',     CollectionType::class,array(
//                'entry_type' => IdProductType::class,
//                'entry_options' => array('label' => false),
//                'allow_add' => true,
//            ))
//            ->add('surname',     TextType::class,[
//                'label' => 'Nom de la Catégorie'
//            ])
//            ->add('time',     IntegerType::class,[
//                'label' => 'Temps de préparation'
//            ])
//    ;
////            ->add('idProduct',     TextType::class,[
////                'label' => 'Id du produit'
////            ])
////        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
////            $data = $event->getData();
////            $form = $event->getForm();
////
////            // check if the Product object is "new"
////            // If no data is passed to the form, the data is "null".
////            // This should be considered a new "Product"
////
////            if ($data != "") {
////                $form->remove('idProduct');
////                $form->add('idProduct', TextType::class ,array(
////                        'attr' => array(
////                            'placeholder' => 'test',
////                        ))
////                );
////            }
////        });
//
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults(array(
//            'data_class' => 'CM\EngravingBundle\Entity\Category'
//        ));
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function getBlockPrefix()
//    {
//        return 'cm_engravingbundle_category';
//    }
//
//
//}

namespace CM\EngravingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('idProduct',     TextType::class,[
                'label' => 'Id du produit'
            ])
            ->add('surname',     TextType::class,[
                'label' => 'Nom de la Catégorie'
            ])
            ->add('time',     IntegerType::class,[
                'label' => 'Temps de gravure en minutes'
            ])
            ->add('folder',     TextType::class,[
                'label' => 'Nom du dossier dans le zip'
            ])
        ;

//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            $data = $event->getData();
//            $form = $event->getForm();
//
//            // check if the Product object is "new"
//            // If no data is passed to the form, the data is "null".
//            // This should be considered a new "Product"
//
//            if ($data != "") {
//                $form->remove('idProduct');
//                $form->add('idProduct', TextType::class ,array(
//                        'attr' => array(
//                            'placeholder' => 'test',
//                        ))
//                );
//            }
//        });

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CM\EngravingBundle\Entity\Category'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cm_engravingbundle_category';
    }


}

