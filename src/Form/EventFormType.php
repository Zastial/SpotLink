<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\EventStatus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class)
            ->add('osm_id', HiddenType::class)
            ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class)
            ->add('location_name')
            ->add('house_number', null, [
                'attr' => ['readonly' => true],
            ])
            ->add('road', null, [
                'attr' => ['readonly' => true],
            ])
            ->add('city', null, [
                'attr' => ['readonly' => true],
            ])
            ->add('postcode', null, [
                'attr' => ['readonly' => true],
            ])
            ->add('state', null, [
                'attr' => ['readonly' => true],
            ])
            ->add('county', null, [
                'attr' => ['readonly' => true],
            ])
            ->add('country', null, [
                'attr' => ['readonly' => true],
            ])
            ->add('date_start', null, [
                'widget' => 'single_text',
            ])
            ->add('date_end', null, [
                'widget' => 'single_text',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
