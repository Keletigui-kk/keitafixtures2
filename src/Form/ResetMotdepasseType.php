<?php

namespace App\Form;

use App\Entity\Users;
use App\Form\Form\FormExtension\RepeatedMotdepasseType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class ResetMotdepasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', RepeatedMotdepasseType::class );  # je fais appelle à ma classe  RepeatedMotdepasseType pour eviter de repeter ici le meme code. Le code est mis dans l'extion RepeatedMotdepasseType.php
    } 
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'  => Users::class
        ]);
    }
}
