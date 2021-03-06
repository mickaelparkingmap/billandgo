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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\DateTime;

class PaimentType extends AbstractType
{
    private $_uid;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->_uid = $options['uid'];
        $builder
            ->add('refBill', EntityType::class, array(
                'class'        => 'BillAndGoBundle:Document',
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->andWhere('c.refUser = '.$this->_uid)
                        ->andWhere('c.type = 0');
                },
                'choice_label' => 'number',
                'multiple'     => false,
                'placeholder' => 'Sélectionnez une facture :'
            ))
            ->add('amount', NumberType::class)
            ->add('datePaiment', DateTimeType::class, array(
                'required' => 'true',
                'widget' => 'single_text',
                'html5' => false,
                'data' => new \DateTime('today')
            ))
            ->add('mode', ChoiceType::class, array(
                'choices' => array(
                    'Espèces' => 'cash',
                    'Chèque' => 'cheque',
                    'Virement bancaire' => 'order'
                )
            ))
            ->add('save', SubmitType::class, array('attr'  => array('class' =>'form-control'), 'label' => "Valider"))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BillAndGoBundle\Entity\Paiment',
            'uid' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'billandgobundle_paiment';
    }
}
