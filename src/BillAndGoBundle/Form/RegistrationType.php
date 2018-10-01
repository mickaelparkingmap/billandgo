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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\OAuthServerBundle\Util\LegacyFormHelper;

class RegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => false, 'translation_domain' => 'FOSUserBundle', 'attr'  => array('class' => 'form-control', 'placeholder' => 'Nom d\'utilisateur')))
            ->add('email', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'), array('label' => false, 'translation_domain' => 'FOSUserBundle', 'attr'  => array('class' => 'form-control', 'placeholder' => "E-mail", 'after' => 'username', 'required' => 'required')))
            ->add('firstname', null, array('label' => false, 'attr'  => array('class' => 'form-control', 'placeholder' => 'Prénom', 'required' => 'required')))
            ->add('lastname', null, array('label' => false, 'attr'  => array('class' => 'form-control', 'placeholder' => 'Nom', 'required' => 'required')))
            ->add('address', null, array('label' => false, 'attr'  => array('class' => 'form-control', 'placeholder' => 'Votre adresse', 'required' => 'required')))
            ->add('zip_code', null, array('label' => false, 'attr'  => array('class' => 'form-control', 'placeholder' => 'Code postal', 'required' => 'required')))
            ->add('city', null, array('label' => false, 'attr'  => array('class' => 'form-control', 'placeholder' => 'Ville', 'required' => 'required')))
            ->add('country', null, array('label' => false, 'attr'  => array('class' => 'form-control', 'placeholder' => 'Pays', 'required' => 'required', 'value' => 'FRANCE', 'disabled' => 'disabled')))
            ->add('phone', null, array('label' => false, 'attr'  => array('class' => 'form-control', 'placeholder' => 'Votre numéro de téléphone', 'required' => false)))
            ->add('mobile', null, array('label' => false, 'attr'  => array('class' => 'form-control', 'placeholder' => 'Votre numéro de mobile', 'required' => 'required')))
            ->add('plainPassword', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\RepeatedType'), array(
                'label' => false,
                'type' => LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'),
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => false, 'attr'  => array('class' => 'form-control', 'placeholder' => 'Votre mot de passe', 'required' => 'required')),
                'second_options' => array('label' =>  false, 'attr'  => array('class' => 'form-control', 'placeholder' => 'Confirmer votre mot de passe', 'required' => 'required')),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('companyname', null, array('label' => false, 'attr'  => array('class' => 'form-control', 'placeholder' => 'Nom de votre société', 'required' => 'required')))
            ->add('job_type', ChoiceType::class, array(
                'choices' => array(
                    'Freelance' => 'freelance',
                    'Micro-entrepreneur' => 'self-entrepreneur'
                ), 'attr'  => array('class' => 'form-control'), 'required' => false, 'placeholder' => 'Votre status', 'label' => false
            ))
        ;
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
