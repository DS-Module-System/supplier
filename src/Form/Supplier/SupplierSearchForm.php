<?php

namespace App\Form\Supplier;

use App\Form\Core\DefaultForm\SearchForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SupplierSearchForm extends SearchForm {
    public function __construct(
        private TranslatorInterface $translator,
        private RequestStack $requestStack,
        private UrlGeneratorInterface $router
    ) {

    }
    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class, [
                'label' => 'name',
                'required' => false,
                'attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('eek', TextType::class, [
                'label' => 'eek',
                'required' => false,
                'attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('vat', TextType::class, [
                'label' => 'vat',
                'required' => false,
                'attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('responsiblePerson', TextType::class, [
                'label' => 'responsiblePerson',
                'required' => false,
                'attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'email',
                'required' => false,
                'attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'phone',
                'required' => false,
                'attr' => [
                    'class' => 'mb-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $resolver->setDefault('action', $this->router->generate($request->get('_route'),
                array_merge($request->get('_route_params'), ['page' => 1])));
        }
        $resolver->setDefault('translation_domain', 'supplier');
    }
} 