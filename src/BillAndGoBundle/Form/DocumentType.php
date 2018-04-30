<?php

namespace BillAndGoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\ORM\EntityRepository;

class DocumentType extends AbstractType
{
    private $_uid;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->_uid = $options['uid'];
        $builder
            //->add('number', TextType::class)
            ->add('description', TextareaType::class, array(
                'attr' => array('class' => 'tinymce')

            ))
            ->add('delayDate', DateTimeType::class, array(
                'required' => 'true',
                'widget' => 'single_text',
                'html5' => false
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
            /*->add('lines', CollectionType::class, array(
                'entry_type' => DevisLineType::class,
                'allow_add' => true,
                'allow_delete' => true
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
            'data_class' => 'BillAndGoBundle\Entity\Document',
            'uid' => NULL
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'billandgobundle_document';
    }


}
