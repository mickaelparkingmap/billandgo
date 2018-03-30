<?php


namespace MyJobManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
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

class LineBillType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->_uid = $options['uid'];
        $builder
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('quantity', IntegerType::class)
            ->add('price', NumberType::class, array(
                'required' => false
            ))
            ->add('refTax', EntityType::class, array(
                'class'        => 'MyJobManagerBundle:Tax',
                'required' => false,
                'choice_label' => 'name',
                'multiple'     => false,
                'placeholder' => 'Sélectionnez une taxe :'
            ))
            /*->add('status', ChoiceType::class, array(
                'choices' => array(
                    'draw' => 'draw',
                    'billed' => 'billed',
                    'paid' => 'paid'
                )
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
            'data_class' => 'MyJobManagerBundle\Entity\Line',
            'uid' => NULL
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'myjobmanagerbundle_line';
    }


}