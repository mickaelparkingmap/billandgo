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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use BillAndGoBundle\Form\BillLineType;
use BillAndGoBundle\Entity\ClientContact;
use Doctrine\ORM\EntityRepository;

class BillType extends AbstractType
{
    private $_uid;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->_uid = $options['uid'];
        $builder
            ->add('number', TextType::class)
            ->add('creation', DateTimeType::class, array(
                'required' => 'true',
                'widget' => 'single_text',
                'html5' => false,
                'data' => new \DateTime()
            ))
            ->add('paimentDate', DateTimeType::class, array(
                'required' => 'true',
                'widget' => 'single_text',
                'html5' => false,
                'data' => new \DateTime()
            ))
            ->add('description', TextareaType::class, array(
                'attr' => array('class' => 'tinymce')

            ))
            /*->add('refContact')*/
            /*->add('refUser')*/
            ->add('client', EntityType::class, array(
                'class'        => 'BillAndGoBundle:Client',
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->andWhere('c.userRef = '.$this->_uid);
                },
                'choice_label' => 'companyName',
                'multiple'     => false,
                'placeholder' => 'Sélectionnez un client :'
            ))
            ->add('lines', CollectionType::class, array(
                'entry_type' => BillLineType::class,
                'allow_add' => true,
                'allow_delete' => true
            ))
            ->add('save', SubmitType::class, array('label' => 'Créer la facture'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BillAndGoBundle\Entity\Bill',
            'uid' => NULL
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'billandgobundle_bill';
    }


}