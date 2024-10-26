<?php

namespace App\Form;

use App\Entity\Measurement;
use App\Entity\WeatherRecord;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MeasurementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
//     {
//         $builder
//             ->add('celsius')
//             ->add('weatherRecord', EntityType::class, [
//                 'class' => WeatherRecord::class,
// 'choice_label' => 'id',
//             ])
//         ;
//     }
    {
        $builder
            ->add('celsius', null, [
                'label' => 'Temperature (°C)',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Temperature is required.',
                    ]),
                    new Assert\Type([
                        'type' => 'numeric',
                        'message' => 'The temperature must be a number.',
                    ]),
                    new Assert\Range([
                        'min' => -100,
                        'max' => 100,
                        'notInRangeMessage' => 'Please enter a temperature between -100 and 100.',
                    ]),
                ],
            ])
            ->add('weatherRecord', EntityType::class, [
                'class' => WeatherRecord::class,
                'choice_label' => function (WeatherRecord $weatherRecord) {
                    // Custom label to show ID, date, and city
                    return sprintf('%d - %s (%s)', 
                        $weatherRecord->getId(), 
                        $weatherRecord->getDate()->format('Y-m-d'), 
                        $weatherRecord->getLocation()->getCity()
                    );
                },
                'label' => 'Weather Record (ID - Date - City)',
                'placeholder' => 'Select a weather record',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please select a weather record.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Measurement::class,
        ]);
    }
}
