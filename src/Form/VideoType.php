<?php

namespace App\Form;

use App\Entity\Video;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('thumbnailUrl')
            ->add('videoUrl')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'movie' => 'Film',
                    'series' => 'Serie',
                ],
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'register',
                'locale' => 'fr',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
