<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 21/03/2018
 * Time: 14:03
 */

namespace GravureBundle\Form\Types;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ProductType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categoryRepository = $options['categoryRepository'];
        $builder
            ->add('alias', TextType::class,[
                'label' => 'Nom du produit complet'
            ])
            ->add('time', IntegerType::class,[
                'label' => 'Temps pour la gravure'
            ])
            ->add('productId', IntegerType::class,[
                'label' => 'Id prestashop du produit'
            ])
            ->add('idCategory', ChoiceType::class, array(
                "label" => "Catégorie",
                "choices" => $this->getArrayCategory($categoryRepository),
            ))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'categoryRepository' => 'GravureBundle\Repository\DbalCategoryRepository'
        ));
    }

    private function getArrayCategory($categoryRepository){
        $categories = $categoryRepository->findAll();
        $arrayCategory = [];
        foreach ($categories as $category){
            $arrayCategory[]=[
                $category['surname'] => $category['id']
            ];
        }
        return $arrayCategory;
    }

}