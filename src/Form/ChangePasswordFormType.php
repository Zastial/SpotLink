<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
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
                'mapped' => false,                       // Ne sera pas sauvegardé, a manipuler dans le controller
                'attr' => ['placeholder' => 'Confirmez votre mot de passe'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez confirmer votre mot de passe.']),
                    new Assert\Callback([$this, 'validatePasswordMatch'])
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
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
