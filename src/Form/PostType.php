<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('images', CollectionType::class,[
                'mapped' => false,
                'entry_type' => FileType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'required' => false,
                'constraints' => [
        new All([
            'constraints' => new File([
                'mimeTypes' => [
                    'image/*'
                ],
                'mimeTypesMessage' => "Only image are allowed",
            ])
        ])
            ]
            ])
            ->add('video', CollectionType::class,[
                'entry_type' => UrlType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'required' => false,
                'mapped' => false
            ])
            ->add('name')
            ->add('content',TextareaType::class,[
                'required' => false

            ])
            ->add('groups')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
