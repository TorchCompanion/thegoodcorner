<?php

namespace App\Type;

use App\Entity\AnnonceCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Annonce title',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Annonce description',
                'attr' => ['class' => 'form-control',
                    'placeholder' => 'Description',],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Annonce price',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'row_attr' => ['class' => 'd-flex flex-column my-3'],
            ])
            ->add('address', TextType::class, [
                'label' => 'Annonce address',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('category', EntityType::class, [
                'class' => AnnonceCategory::class,
                'choice_label' => 'name',
            ])
            ->add('pictures', CollectionType::class, [
                'label' => 'Annonce pictures',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Post annonce',
                'attr' => ['class' => 'btn btn-primary'],
            ]);


    }
}