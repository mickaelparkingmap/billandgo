<?php

namespace BillAndGoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ClientContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, array('label' => 'Nom'))
            ->add('firstname', TextType::class, array('label' => 'Prénom'))
            ->add('email', EmailType::class, array('label' => 'E-mail'))
            ->add('phone', TextType::class, array('label' => 'Téléphone'))
            ->add('mobile', TextType::class, array('label' => 'Mobile', 'attr'  => array('class' => 'form-control')))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BillAndGoBundle\Entity\ClientContact'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'billandgobundle_clientcontact';
    }


}
