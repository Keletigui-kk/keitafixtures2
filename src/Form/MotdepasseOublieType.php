<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class MotdepasseOublieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void    # void veut dire ça ne retounre rien
    {
        $builder
            ->add('email', RepeatedType::class, [
                'type'            => EmailType::class,
                'invalid_message' => "Les adresses e-mails doivent être identiques.",
                'required'        => true,
                'constraints'     => [
                    new NotBlank(),    # notblank veut dire ne doit pas être nul
                    new Email()       # pour verifier si l'email saigit est valide
                ],
                'first_options'    =>[
                    'label' => 'Saisir votre adresse e-mail'
                ],
                'second_options'    =>[
                    'label' => 'Confirmez votre adresse e-mail'
                ]
            ]);
           
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => Users::class, ici il ne faut pas donner la class data pour ne pas donner l'in formation à celui qui veut recuperer le mot de passe si c'est une arnaque par exemple
        ]);
    }
}
