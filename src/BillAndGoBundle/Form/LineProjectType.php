<?php
/**
 * Created by PhpStorm.
 * User: mickael
 * Date: 04/10/17
 * Time: 14:57
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

class LineProjectType extends AbstractType
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
            ->add('refTax', EntityType::class, array(
                'class'        => 'BillAndGoBundle:Tax',
                'required' => false,
                'choice_label' => 'name',
                'multiple'     => false,
                'placeholder' => 'Sélectionnez une taxe :'
            ))
            ->add('estimatedTime', NumberType::class, array(
                'required' => false
            ))
            /*->add('status', ChoiceType::class, array(
                'choices' => array(
                    'planned' => 'planned',
                    'working on it' => 'working',
                    'waiting for client validation' => 'waiting',
                    'validated' => 'validated'
                )
            ))*/
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