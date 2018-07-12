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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ProjectType extends AbstractType
{
    private $_uid;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->_uid = $options['uid'];
        $builder
            ->add('name', TextType::class)
            //->add('begin', DateTimeType::class, array('required' => 'true'))
            ->add('deadline', DateTimeType::class, array(
                'required' => 'true',
                'widget' => 'single_text',
                'html5' => false
            ))
            ->add('description', TextareaType::class, array(
                'attr' => array('class' => 'tinymce')

            ))
            ->add('refClient', EntityType::class, array(
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
            ->add('save', SubmitType::class, array('label' => 'Oui'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BillAndGoBundle\Entity\Project',
            'uid' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'billandgobundle_project';
    }
}
