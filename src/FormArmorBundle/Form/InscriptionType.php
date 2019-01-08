<?php

namespace FormArmorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('client', EntityType::class, array('class' => 'FormArmorBundle:Client', 'choice_label' => 'nom', 'multiple' => false))
            ->add('sessionFormation', EntityType::class, array('class' => 'FormArmorBundle:Session_formation', 'choice_label' => 'affichage', 'multiple' => false))
			->add('dateInscription', DateType::class)
            ->add('Valider', SubmitType::class)
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FormArmorBundle\Entity\Inscription'
        ));
    }
}
