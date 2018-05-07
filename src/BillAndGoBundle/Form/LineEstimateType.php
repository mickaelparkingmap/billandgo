<?php

/**
 *
 *  * This is an iumio component [https://iumio.com]
 *  *
 *  * (c) Mickael Buliard <mickael.buliard@iumio.com>
 *  *
 *  * Bill&Go, gérer votre administratif efficacement [https://billandgo.fr]
 *  *
 *  * To get more information about licence, please check the licence file
 *
 */


namespace BillAndGoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LineEstimateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->_uid = $options['uid'];
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('quantity', IntegerType::class)
            ->add('price', NumberType::class, array(
                'required' => true
            ))
            ->add('refTax', EntityType::class, array(
                'class'        => 'BillAndGoBundle:Tax',
                'required' => true,
                'choice_label' => 'name',
                'multiple'     => false,
                'placeholder' => 'Sélectionnez une taxe :'
            ))
            ->add('estimatedTime', NumberType::class, array(
                'required' => false
            ))
            /*->add('status', ChoiceType::class, array(
                'choices' => array(
                    'draw' => 'draw',
                    'estimated' => 'estimated',
                    'accepted' => 'accepted',
                    'canceled' => 'canceled'
                )
            ))*/
            /*->add('deadline', DateTimeType::class, array(
                'required' => 'false',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => array('class' => 'ddl')
            ))*/
            ->add('save', SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BillAndGoBundle\Entity\Line',
            'uid' => NULL
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'billandgobundle_line';
    }


}