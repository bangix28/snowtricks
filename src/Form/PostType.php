<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('thumbnail',FileType::class,[
                'data_class' => null,
                'required' => false
            ])
            ->add('images', CollectionType::class,[
                'mapped' => false,
                'entry_type' => FileType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => ['label'=> false]


            ])
            ->add('video', CollectionType::class,[
                'entry_type' => UrlType::class,
                'entry_options' => [
                    'attr' => ['class' => 'email-box'],
                ],
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('name')
            ->add('content')
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
