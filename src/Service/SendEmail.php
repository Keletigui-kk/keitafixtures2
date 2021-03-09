<?php

namespace App\Service;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;


class SendEmail 
{
    private MailerInterface $mailer;

    private string $senderEmail;

    private string $senderName;

    public function __construct(
        MailerInterface $mailer,
        string $senderEmail,
        string $senderName
    )
    {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
    }

    # on crée la fonction qui va envoyer l'email
    /**@param array<mixed> $arguments */
    public function send(array $arguments): void
    {  
       [
           'recipient_email' => $recipientEmail,
           'subject'          => $subject,
           'html_template'    => $htmltTemplate,
           'context'          => $context
       ] = $arguments;     # c'est la destructuration

       $email = new TemplatedEmail();

       $email->from(new Address($this->senderEmail, $this->senderName))
             ->to($recipientEmail)
             ->subject($subject) 
             ->htmlTemplate($htmltTemplate)
             ->context($context); 
        try{
            $this->mailer->send($email);
        }catch(TransportExceptionInterface $mailerException){
            throw $mailerException;
        }
        # à partir de la on peut envoyer un email
    }

}