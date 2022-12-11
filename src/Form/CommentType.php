<?php

namespace App\Form;

use App\Entity\Comment;
// use App\Entity\User;
// use App\Entity\Episode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
// use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextType::class)
            ->add('rate', ChoiceType::class, [
                'choices' => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
            ]);
        // ->add('author', EntityType::class, [
        //     'class' => User::class,
        //     'choice_label' => 'username',
        //     'multiple' => false,
        //     'expanded' => false,
        //     'by_reference' => false
        // ])
        // ->add('episode', EntityType::class, [
        //     'class' => Episode::class,
        //     'choice_label' => 'number',
        //     'multiple' => false,
        //     'expanded' => false,
        //     'by_reference' => false
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}