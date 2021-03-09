<?php

# on fait une extension poue faire  le reset du mot de passre
namespace App\Form\Form\FormExtension;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class RepeatedMotdepasseType extends AbstractType{
    public function getParent(): string   # pour modidier la classe reapetedType
    {
        return RepeatedType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([     # on met les options de mot de passe ici copier à partir du formulaire d'inscription
                'type'             => PasswordType::class,
                'invalid_message'  => "Les mots de passe saisis ne correspondent pas.",
                'required'         => true,
                'mapped'           => false,
                'first_options'    =>[
                    'label'        => 'Mot de passe',
                    "attr" => [
                        "class" =>"form-control",
                        // 'pattern' => '' avoir...
                    ]
                ],
                'second_options' =>[
                    'label' => 'Confirmez votre mot de passe',
                    "attr" => [
                        "class" =>"form-control",
                        // 'pattern' => '' a voir ...
                    ]
                ],
               
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre mot de passe s\'il vous plait',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            
        ]);
    }
}

