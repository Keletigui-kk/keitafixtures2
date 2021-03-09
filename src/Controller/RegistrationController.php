<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\UserAutenticathorAuthenticator;
use App\Service\SendEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request, 
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder, 
        // GuardAuthenticatorHandler $guardHandler, 
        // UserAutenticathorAuthenticator $authenticator,
        SendEmail $sendEmail,
        TokenGeneratorInterface $tokenGenerator    # rajouter pour gerer l'attribut registrationToken qui est dans l'entité users et qui est cité plus bas dans le code
        ): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registrationToken = $tokenGenerator->generateToken();
            $user->setRegistrationToken($registrationToken)
            // encode the plain password
                  ->setPassword($passwordEncoder->encodePassword($user,$form->get('plainPassword')->getData()));
          

            // $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            # j'utilise le service sendEmail crée dans le fichier services.yaml
            $sendEmail->send([
                'recipient_email' => $user->getEmail(),  # l'email qui à été saisi par l'utilisateur
                'subject'         => 'Verification de votre adresse email pour activer votre compte utilisateur',
                'html_template'   => "registration/register_confirmation_email.html.twig",
                'context'         =>[
                    'userID'  => $user->getId(),
                    'registrationToken'  => $registrationToken,
                    'tokenLifeTime'     => ($user->getAccountMustBeVerifiedBefore())->format('d/m/Y à H:i')
                ]
            ]);
             # rajout d'elements pour l'envoie d'email
             $this->addFlash('success', "Votre compte utilisateur a bien été crée, verifiez vos e-mails pour l'activer");
             # redirection vers la page login

            return $this->redirectToRoute('app_login');

            // return $guardHandler->authenticateUserAndHandleSuccess(
            //     $user,
            //     $request,
            //     $authenticator,
            //     'main' ,// firewall name in security.yaml
            // );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    # on crée le chemin app_verify_account: utilisé dans le fichier registration_confiraltion_email qui permet de verifier le compte

    /**
     * @Route("/{id<\d+>}/{token}", name="app_verify_account", methods={"GET"})    #{id}/{token}: sont des variables; <\d+>: veut que l'id sera 1 ou +sieurs nombres comme c'est un id numerique ou +sieurs chiffres
     */
    public function verifyUserAccount( 
        EntityManagerInterface $entityManager,
        Users $user,
        string $token
    ): Response
    {
        # on fait des conditions
        if(($user->getRegistrationToken() === null) || ($user->getRegistrationToken() !== $token) || ($this->isNotRequestedInTime($user->getAccountMustBeVerifiedBefore() ))){
            throw new AccessDeniedException();   # pour envoyer une erreur s'il depasse lez delai d'activation du compte
        } # s'il ne clic pas sur le lien de confirmation à  temps
       
        $user->setIsverified(true);    
        $user->setAccountVerifiedAt(new \DateTimeImmutable('now'));  # on specifie la date de validation du compte
        $user->setRegistrationToken(null);   # pour ennuler le token pour qu'il ne soit plus utilisable
        $entityManager->flush();
        $this->addFlash('success', 'Votre compte utilisateur est dès à présent activer vous pouvez vous connecter !');

        return $this->redirectToRoute('app_login');  # redirection vers le login
    }
   
    # on crée la fonction privé isNotRequestedInTime qui est declaré en methode ci dessus

    private function isNotRequestedInTime(\DateTimeImmutable $accountMustBeVerifiedBefore): bool
    {
        return (new \DateTimeImmutable('now') > $accountMustBeVerifiedBefore); # si la date courante est > à la accountMustBeVerifiedBefore, ca retournera true si non ca retoune false
    }
}
