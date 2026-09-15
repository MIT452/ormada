<?php

namespace App\Form;

use App\Entity\Actualite;
use App\Entity\Evenement;
use App\Entity\Media;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', FileType::class, [
                            'label' => 'Image',
                            'mapped' => false,
                            'required' => true,
                            'constraints' => [
                                new File([
                                    'maxSize' => '2M',
                                    'mimeTypes' => [
                                        'image/jpeg',
                                        'image/png',
                                        'image/jpg',
                                    ],
                                    'mimeTypesMessage' => 'Veuillez uploader une image valide',
                                ])
                            ],
                        ])

            ->add('actualite', EntityType::class, [
                'class' => Actualite::class,
                'choice_label' => 'id',
            ])
            ->add('evenement', EntityType::class, [
                'class' => Evenement::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
