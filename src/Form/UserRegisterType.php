<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Prénom'],
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Nom'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => ['placeholder' => 'Email'],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,                      // Ne sera pas sauvegardé directement dans l'entité User, a manipuler dans le controller
                'attr' => ['placeholder' => 'Mot de passe'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer un mot de passe.']),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins 6 caractères.',
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmer votre mot de passe',
                'mapped' => false,                       // Ne sera pas sauvegardé directement dans l'entité User, a manipuler dans le controller
                'attr' => ['placeholder' => 'Confirmez votre mot de passe'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez confirmer votre mot de passe.']),
                    new Assert\Callback([$this, 'validatePasswordMatch'])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['register']
        ]);
    }

    public function validatePasswordMatch($confirmPassword, ExecutionContextInterface $context): void
    {
        $form = $context->getRoot(); // Récupérer le formulaire parent
        $password = $form->get('password')->getData();

        if ($password !== $confirmPassword) {
            $form->get('confirmPassword')->addError(new \Symfony\Component\Form\FormError('Les mots de passe ne correspondent pas.'));

        }
    }
}
