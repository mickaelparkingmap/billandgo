<?php


/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BillAndGoBundle\Form;

use BillAndGoBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
class ProfileType extends AbstractType
{
    /**
     * @var string
     */
    private $class;
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildUserForm($builder, $options);
        $constraintsOptions = array(
            'message' => 'fos_user.current_password.invalid',
        );
        if (!empty($options['validation_groups'])) {
            $constraintsOptions['groups'] = array(reset($options['validation_groups']));
        }
        $builder
            ->add('email',EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'attr'  => array('class' => 'form-control')))
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
            ->add('job_type', ChoiceType::class, array(
                'choices' => array(
                    'Freelance' => 'freelance',
                    'Micro-entrepreneur' => 'self-entrepreneur'
                ), 'attr'  => array('class' => 'form-control'), 'required' => false, 'placeholder' => 'Non défini'
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password', 'attr'  => array('class' => 'form-control')),
                'second_options' => array('label' => 'form.password_confirmation', 'attr'  => array('class' => 'form-control')),
                'invalid_message' => 'fos_user.password.mismatch',
            ));
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'csrf_token_id' => 'profile',
            // BC for SF < 2.8
            'intention' => 'profile',
        ));
    }


    // BC for SF < 3.0
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fos_user_profile';
    }
    /**
     * Builds the embedded form representing the user.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('email', EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
        ;
    }
}
