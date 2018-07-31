<?php

/**
 *
 *  * This is an iumio component [https://iumio.com]
 *  *
 *  * (c) Mickael Buliard <mickael.buliard@iumio.com>
 *  *
 *  * Bill&Go, gérer votre administratif efficacement [https://www.billandgo.fr]
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

class LineType extends AbstractType
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
                'required' => false
            ))
            ->add('vat', NumberType::class, array(
                'required' => false
            ))
            ->add('estimatedTime', NumberType::class, array(
                'required' => false
            ))
            ->add('status', ChoiceType::class, array(
                'choices' => array(
                    'draw' => 'draw',
                    'estimated' => 'estimated',
                    'accepted' => 'accepted',
                    'planned' => 'planned',
                    'working on it' => 'working',
                    'waiting for client validation' => 'waiting',
                    'validated' => 'validated',
                    'billing' => 'billing',
                    'billed' => 'billed',
                    'canceled' => 'canceled'
                )
            ))
            ->add('deadline', DateTimeType::class, array(
                'required' => 'false',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => array('class' => 'ddl')
            ))
            ->add('save', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BillAndGoBundle\Entity\Line',
            'uid' => null
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
