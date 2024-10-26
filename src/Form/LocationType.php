<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter city name',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'City name is required']),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'City name cannot be longer than {{ limit }} characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[^\d]*$/', // This regex allows only non-digit characters
                        'message' => 'City name cannot contain numbers.',
                    ]),
                ],
            ])
            ->add('country', ChoiceType::class, [
                'placeholder' => 'Select country',
                'choices' => [
                    'Poland' => 'PL',
                    'Germany' => 'DE',
                    'France' => 'FR',
                    'Spain' => 'ES',
                    'Italy' => 'IT',
                    'United Kingdom' => 'GB',
                    'United States' => 'US',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Country selection is required']),
                ],
            ])
            ->add('voivodeship', TextType::class, [
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'Voivodeship cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('zipcode', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Zip code is required']),
                    new Assert\Length([
                        'max' => 10,
                        'maxMessage' => 'Zip code cannot be longer than {{ limit }} characters',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^\d{2}-\d{3}$/',
                        'message' => 'Zip code must be in the format 00-000',
                    ]),
                ],
            ])
            ->add('latitude', NumberType::class, [
                'scale' => 7,  // precise latitude with 7 decimal places
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Latitude is required']),
                    new Assert\Range([
                        'min' => -90,
                        'max' => 90,
                        'notInRangeMessage' => 'Latitude must be between {{ min }} and {{ max }}',
                    ]),
                ],
            ])
            ->add('longitude', NumberType::class, [
                'scale' => 7,  // precise longitude with 7 decimal places
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Longitude is required']),
                    new Assert\Range([
                        'min' => -180,
                        'max' => 180,
                        'notInRangeMessage' => 'Longitude must be between {{ min }} and {{ max }}',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
            'validation_groups' => function ($form) {
                // Custom validation groups for 'edit' and 'new'
                return $form->getData()->getId() ? ['edit'] : ['new'];
            },
        ]);
    }
    // public function configureOptions(OptionsResolver $resolver)
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Location::class,
    //         'validation_groups' => ['create'],
    //     ]);
    // }
}

