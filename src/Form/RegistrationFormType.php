<?php

namespace App\Form;

use App\Entity\Users;
use App\Form\Form\FormExtension\RepeatedMotdepasseType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
// use Symfony\Component\Form\Extension\Core\Type\PasswordType;
// use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                "attr" => [
                    "class" =>"form-control",
                    'placeholder' => 'Nom',
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom',
                "attr" => [
                    "class" =>"form-control",
                    'placeholder' => 'Prenom',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                "attr" => [
                    "class" =>"form-control",
                    'placeholder' => 'Email',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre mail s\'il vous plait',
                        // 'message' => "L'email {{ value}} n'est pas valide",
                    ]),
                ]    
            ])
           
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Acceptez les termes',
                "attr" => [
                    "class" =>"form-check-input ",
                ],
                'mapped' => false,
                'required'  => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez acceptez les termes.',
                    ]),
                ],
            ]) 
            ->add('plainPassword', RepeatedMotdepasseType::class )  # RepeatedMotdepasseType: remplace la clase RepeatedType ici. on a mis le contenu de la partie passe word dans repeatedMotdepasseType.php pour eviter la repetition du code
             //[
            //     // instead of being set onto the object directly,
            //     // this is read and encoded in the controller
            //     'type'   => PasswordType::class,
            //     'invalid_message' => "Les mots de passe saisis ne correspondent pas.",
            //     'required'  => true,
            //     'mapped' => false,   # ça veut dire qu'il n'est pas lié à la base de données donc à l'entité
            //     'first_options' =>[
            //         'label' => 'Mot de passe',
            //         "attr" => [
            //             "class" =>"form-control",
            //             // 'pattern' => '' avoir...
            //         ]
            //     ],
            //     'second_options' =>[
            //         'label' => 'Confirmez votre mot de passe',
            //         "attr" => [
            //             "class" =>"form-control",
            //             // 'pattern' => '' a voir ...
            //         ]
            //     ],
               
            //     'constraints' => [
            //         new NotBlank([
            //             'message' => 'Entrez votre mot de passe s\'il vous plait',
            //         ]),
            //         new Length([
            //             'min' => 6,
            //             'minMessage' => 'Your password should be at least {{ limit }} characters',
            //             // max length allowed by Symfony for security reasons
            //             'max' => 4096,
            //         ]),
            //     ],
            // ]
           

            ->add('submit', SubmitType::class, [
                'label' => 'Inscription',
                'attr' => [
                    'class' => 'btn btn-success mt-2 btn-block'
                ]
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
