<?php


namespace MyJobManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Util\LegacyFormHelper;


class RegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'), array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'attr'  => array('class' => 'form-control')))
            ->add('username', null, array('label' => 'Nom d\'utilisateur', 'translation_domain' => 'FOSUserBundle', 'attr'  => array('class' => 'form-control')))
            ->add('companyname', null, array('label' => 'Nom de votre société', 'attr'  => array('class' => 'form-control')))
            ->add('firstname', null, array('label' => 'Prénom', 'attr'  => array('class' => 'form-control')))
            ->add('lastname', null, array('label' => 'Nom', 'attr'  => array('class' => 'form-control')))
            ->add('address', null, array('label' => 'Adresse', 'attr'  => array('class' => 'form-control')))
            ->add('zip_code', null, array('label' => 'Code postal', 'attr'  => array('class' => 'form-control')))
            ->add('city', null, array('label' => 'Ville', 'attr'  => array('class' => 'form-control')))
            ->add('country', null, array('label' => 'Pays', 'attr'  => array('class' => 'form-control')))
            ->add('phone', null, array('label' => 'Numéro de téléphone fixe', 'attr'  => array('class' => 'form-control')))
            ->add('mobile', null, array('label' => 'Numéro de téléphone mobile', 'attr'  => array('class' => 'form-control')))
            ->add('plainPassword', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\RepeatedType'), array(
                'type' => LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'),
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password', 'attr'  => array('class' => 'form-control')),
                'second_options' => array('label' => 'form.password_confirmation', 'attr'  => array('class' => 'form-control')),
                'invalid_message' => 'fos_user.password.mismatch',
            ));
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