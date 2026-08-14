<?php

namespace App\Form\Supplier;

use App\Entity\Supplier\Supplier;
use App\Form\Core\DefaultForm\EditForm;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SupplierForm extends EditForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('name', TextType::class, [
                'label' => 'name',
                'label_attr' => [
                    'class' => 'bg'
                ],
                'required' => false,
            ])
            ->add('eek', TextType::class, [
                'label' => 'eek',
                'required' => false,
            ])
            ->add('vat', TextType::class, [
                'label' => 'vat',
                'required' => false,
            ])
            ->add('responsiblePerson', TextType::class, [
                'label' => 'responsiblePerson',
                'required' => false,
                'label_attr' => [
                    'class' => 'bg'
                ],
            ])
            ->add('email', TextType::class, [
                'label' => 'email',
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'label' => 'phone',
                'required' => false,
            ])
            ->add('address', TextareaType::class, [
                'label' => 'address',
                'required' => false,
                'label_attr' => [
                    'class' => 'bg'
                ],
            ])
            ->add('countryCode', ChoiceType::class, [
                'label' => 'countryCode',
                'choices' => [
                    "Austria" => "AT",
                    "Belgium" => "BE",
                    "Bulgaria" => "BG",
                    "Cyprus" => "CY",
                    "Czechia" => "CZ",
                    "Germany" => "DE",
                    "Denmark" => "DK",
                    "Estonia" => "EE",
                    "Greece" => "EL",
                    "Spain" => "ES",
                    "Finland" => "FI",
                    "France" => "FR",
                    "Croatia" => "HR",
                    "Hungary" => "HU",
                    "Ireland" => "IE",
                    "Italy" => "IT",
                    "Lithuania" => "LT",
                    "Luxembourg" => "LU",
                    "Latvia" => "LV",
                    "Malta" => "MT",
                    "The Netherlands" => "NL",
                    "Poland" => "PL",
                    "Portugal" => "PT",
                    "Romania" => "RO",
                    "Sweden" => "SE",
                    "Slovenia" => "SI",
                    "Slovakia" => "SK",
                    "Northern Ireland" => "XI",
                ],
                'choice_label' => function ($choice, string $key, mixed $value): string {
                    return "{$value}-{$key}";
                },
                'required' => false,
            ])
            ->add('supplierNumber', TextType::class, [
                'label' => 'supplierNumber',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Supplier::class,
            'translation_domain' => 'supplier',
        ]);
    }
} 