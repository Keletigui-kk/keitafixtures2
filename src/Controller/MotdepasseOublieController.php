<?php

namespace App\Controller;
use App\Entity\Users;
use App\Form\MotdepasseOublieType;
use App\Form\ResetMotdepasseType;
use App\Repository\UsersRepository;
use App\Service\SendEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class MotdepasseOublieController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private SessionInterface $session;

    private UsersRepository $usersRepository;

    public function __construct(
        # LES DIFFERENTS SRVICES DONT ON AURA BESOIN DANS LES DIFFRENTES METHODES
        EntityManagerInterface $entityManager,
        SessionInterface $session,
        UsersRepository $usersRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->usersRepository = $usersRepository;
    }

    /**
     * @Route("/motdepasse-oublie", name="app_motdepasse_oublie", methods={"GET", "POST"})
     */
    public function sendRecoveryLink(
        Request $request,
        SendEmail $sendEmail,
        TokenGeneratorInterface $tokenGenerator    # c'est lui qui genere les token à envoyer
    ): Response
    {
        # on crée un formulaire
        $form = $this->createForm(MotdepasseOublieType::class);  # on exporte le formulaire qu'il ya dans: MotdepasseOublieType
        $form->handleRequest($request);
        
        # on verifie si le formulaire est soumis
        if($form->isSubmitted() && $form->isValid()){
            $user = $this->usersRepository->findOneBy([
                'email' => $form['email']->getData()
            ]);
             # on fait un lure s'il n'ya pas d'utilisateur: on affiche le meme message si l'utilisateur existe ou pas dans la base de données comme ca on ne donne pas d'informations
            if(!$user){
                $this->addFlash('success', 'Un email vous a été envoyé pour  redefinir votre mot de passe');
                
                return $this->redirectToRoute('app_login');   # on redirige vers le login
            } 
            
             $user->setMotdepasseOublieToken($tokenGenerator->generateToken())
                  ->setForgotPasswordTokenRequestedAt(new \DateTimeImmutable('now')) 
                  ->setForgotPasswordTokenMustBeVerifiedBefore(new \DateTimeImmutable('+15 minutes'));  # le mot de passe doit être verifier dans 15 minutes
            
            $this->entityManager->flush(); 
            # on envoie l'email
            $sendEmail->send([
                'recipient_email' => $user->getEmail(),
                'subject'         => 'Modification de votre mot de passe',
                'html_template'   => 'motdepasse_oublie/motdepasse_oublie_email.html.twig',
                'context'         => [
                    'user'      => $user
                ] 
            ]); 

            $this->addFlash('success', 'Un email vous a été envoyé pour  redefinir votre mot de passe');
            
            return $this->redirectToRoute('app_login');   # on redirige vers le login

        }
        return $this->render('motdepasse_oublie/motdepasse_oublie_etape1.html.twig', [
            'motdepasseOublieFormEtape1' => $form->createView(),       # envoie du formulaire à la vue
        ]); 
    }

    # on crée la fonction retrieve_credentials evoqueé dans le template
    /**
     * @Route("/motdepasse-oublie/{id<\d+>}/{token}", name="app_retrieve_credentials", methods={"GET"})
     */
    public function retrieveCredentialsFromTheURL(
        string $token,
        Users $user
    ): RedirectResponse
    {
        $this->session->set('Reset-Password-Token-URL', $token);
        $this->session->set('Reset-Password-User-Email', $user->getEmail());

        return $this->redirectToRoute('app_reset_password');   # on redirige vers le le set_password

    }

    /**
     * @Route("/reset-password", name="app_reset_password", methods={"GET", "POST"})
     */
    public function resetPassword(
        Request $request,
        UserPasswordEncoderInterface $encoder
    ): Response
    {
        [
            'token'     => $token,
            'userEmail' => $userEmail
        ] = $this->getCredentialsFromSession();

        # on verifie si le user existe
        $user = $this->usersRepository->findOneBy([
            'email'  => $userEmail
        ]);

        if (!$user){
            return $this->redirectToRoute('app_motdepasse_oublie');
        }

       
        $forgotPasswordTokenMustBeVerifiedBefore = $user->getForgotPasswordTokenMustBeVerifiedBefore();

        # CONDITIONS
        if (($user->getMotdepasseOublieToken() === null) || ($user->getMotdepasseOublieToken() !== $token) || ( $this->isNotRequestedInTime($forgotPasswordTokenMustBeVerifiedBefore))){    #isNotRequestedInTime: mehod privé qu'on va definir plus bas et qui qui sera pris en compte s'il ne clic pas à temps sur le lien
            return $this->redirectToRoute('app_motdepasse_oublie');
        }

        # on genere le formulaire
        $form = $this->createForm(ResetMotdepasseType::class, $user);
        $form->handleRequest($request);

        # on verifie si le formulaire est soumis && valide
        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($encoder->encodePassword($user, $form['plainPassword']->getData()));
            
            # on  va supprimer le token: l'ancien mot  de passe devient inutisable
            /* We clear the token to make it unusable. */
            $user->setMotdepasseOublieToken(null)
                 ->setForgotPasswordVerifiedAt(new \DateTimeImmutable('now'));  # on regarde quant est ce que le token à été verifié

            $this->entityManager->flush();

             # on supprime les credentials de la  session avec une methode privée removeCredentialsFormSession
            $this->removeCredentialsFromSession();

             # j'envoie un message flash

            $this->addFlash('success', 'Votre mot de passe à été modifié, vous pouvez à présent vous connecter.');

            return $this->redirectToRoute('app_login');   # on redirige vers le login
        }

        # on retoune le formulaire à la vue
        return $this->render('motdepasse_oublie/motdepasse_oublie_etape2.html.twig', [
            'motdepasseOublieFormEtape2' => $form->createView(),       # envoie du formulaire à la vue
            'passwordMustBeModifiedBefore' => $this->passwordMustBeModifiedBefore($user)  #  passwordMustVerifiedBefore metho privée qui sera crée en bas.
        ]);
    }

     # on cree la metho privée removeCredentialsFormSession()
    /**
     * Gets the user ID and token from session
     * 
     * @return array
     */

    private function getCredentialsFromSession(): array
    {
        return [
            'token'     => $this->session->get('Reset-Password-Token-URL'),
            'userEmail' => $this->session->get('Reset-Password-User-Email')
        ];
    }

    # method privé isNotRequestedInTime
    /**
     * Valider ou non que le lien à été cliqué dans les temps
     * 
     * @param \DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore
     * @return bool
     */
    public function isNotRequestedInTime(\DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore): bool  # on compare le datetime courant au datetome contenu dans la variable(la valeur qu'on rentre pour cet user dans la dase de donees): si c'est > ca retourne true  et inversement
    {
        return (new \DateTimeImmutable('now') > $forgotPasswordTokenMustBeVerifiedBefore );
    }

    # on cree la metho privée removeCredentialsFormSession()
    /**
     * Supprime l'ID du user et le token à partir de la session'
     * 
     * @return void
     */
    public function removeCredentialsFromSession(): void
    {
        $this->session->remove('Reset-Password-Token-URL');
        $this->session->remove('Reset-Password-User-Email');
    }

    # on crée la methode privée passwordMustBeModifiedBefore

    /**
     * Returns thee time before which the password must be changed
     * 
     * @param Users $user
     * @return string the time in this format: 12h00
     */
    private function passwordMustBeModifiedBefore(Users $user): string
    {
        /** @var \DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore */
        $passwordMustBeModifiedBefore = $user->getForgotPasswordTokenMustBeVerifiedBefore();

        return $passwordMustBeModifiedBefore->format('H\hi');  # H pour les heures \h pour mettre un h string et i pour mes minutes
    }
}
