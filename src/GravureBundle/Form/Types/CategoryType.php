<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 16/03/2018
 * Time: 12:29
 */

namespace GravureBundle\Form\Types;

use GravureBundle\Entity\Domain\Machine;
use GravureBundle\Repository\DbalMachineRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $machineRepository = $options['machineRepository'];
        $builder
            ->add('surname', TextType::class,[
                'label' => 'Surnom'
            ])
            ->add('folder', TextType::class,[
                'label' => 'Nom du dossier'
            ])
            ->add('nameGabarit', TextType::class,[
                'label' => 'Nom du gabarit'
            ])
            ->add('maxGabarit', IntegerType::class,[
                'label' => 'Nombre de gravure maximum du gabarit'
            ])
            ->add('pathGabarit', FileType::class, array(
                'required' => false,
                'data_class' => null,
                'label' => 'Image du gabarit'
            ))
            ->add('idMachine', ChoiceType::class, array(
                "label" => "Type de machine obligatoire",
                "choices" => $this->getArrayMachine($machineRepository),
            ))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'machineRepository' => 'GravureBundle\Repository\DbalMachineRepository'
        ));
    }

    private function getArrayMachine($machineRepository){
        $machines = $machineRepository->findAll();
        $arrayMachine = [];
//        $arrayMachine[] = ['null' => null];
        foreach ($machines as $machine){
            $arrayMachine[]=[
                $machine['name'] => $machine['id']
            ];
        }
        return $arrayMachine;
    }
}