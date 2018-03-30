<?php

namespace MyJobManagerBundle\Form;

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
                'class'        => 'MyJobManagerBundle:Client',
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->andWhere('c.userRef = '.$this->_uid);
                },
                'choice_label' => 'companyName',
                'multiple'     => false,
                'placeholder' => 'Sélectionnez un client :'
            ))
            ->add('save', SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MyJobManagerBundle\Entity\Project',
            'uid' => NULL
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'myjobmanagerbundle_project';
    }


}