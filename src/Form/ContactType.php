<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => true,
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Votre nom',
                    'class' => 'form-control'
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'votre.email@example.com',
                    'class' => 'form-control'
                ]
            ])
            ->add('titre', TextType::class, [
                'required' => true,
                'label' => 'Titre/Sujet',
                'attr' => [
                    'placeholder' => 'Sujet de votre message',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'label' => 'Message',
                'attr' => [
                    'placeholder' => 'Votre message',
                    'rows' => 4,
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'contact_form'
        ]);
    }
}
