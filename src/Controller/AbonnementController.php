<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AbonnementController extends AbstractController
{
    /**
     * @Route("/abonnement", name="abonnement")
     */
    public function index(): Response
    {
        return $this->render('abonnement/index.html.twig', [
           
        ]);
    } 
    
    # on cree 2 url: une de succes et une d'erreur

    /**
     * @Route("/success", name="success")
     */
    public function success(): Response
    {
        return $this->render('abonnement/success.html.twig', [
           
        ]);
    }
    
    /**
     * @Route("error", name="error")
     */
    public function error(): Response
    {
        return $this->render('abonnement/error.html.twig', [
           
        ]);
    }

     # Creation d'une nouvelle fonction et d'une nouvelle route pour le payement

    /**
     * @Route("/create-checkout-session", name="checkout")
     */
    public function checkout(): Response
    {
        # clé privée de stripe
        \Stripe\Stripe::setApiKey('sk_test_51Hwb1rDqg9iWMtezpu1OkF1zDvHtg61jfJXPpIkU1MNibXOL65y3n6hrS1zHWhqnV9ObD6VvNUiiZcSHNmDcSs5W00gk3d6u4w');        
        # INITIALISER UNE SESSION POUR METTRE LES INFOS QUE L'ON VEUT DANS LE SYSTEME DE PAYEMENT
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                'name' => 'T-shirt',
                ],
                'unit_amount' => 2000,
            ],
            'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),  # url absolue de la page success
            'cancel_url' => $this->generateUrl('error', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
      
         return new JsonResponse([ 'id' => $session->id ]);
      
    }  



     /**
     * @Route("/create-checkout-session1", name="checkout1")
     */

    public function checkout1(): Response
    {
        # clé privée de stripe
        \Stripe\Stripe::setApiKey('sk_test_51Hwb1rDqg9iWMtezpu1OkF1zDvHtg61jfJXPpIkU1MNibXOL65y3n6hrS1zHWhqnV9ObD6VvNUiiZcSHNmDcSs5W00gk3d6u4w');        
        # INITIALISER UNE SESSION POUR METTRE LES INFOS QUE L'ON VEUT DANS LE SYSTEME DE PAYEMENT
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                'name' => 'T-shirt',
                ],
                'unit_amount' => 5000,
            ],
            'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),  # url absolue de la page success
            'cancel_url' => $this->generateUrl('error', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
      
         return new JsonResponse([ 'id' => $session->id ]);
      
    }  



    /**
     * @Route("/create-checkout-session2", name="checkout2")
     */

    public function checkout2(): Response
    {
        # clé privée de stripe
        \Stripe\Stripe::setApiKey('sk_test_51Hwb1rDqg9iWMtezpu1OkF1zDvHtg61jfJXPpIkU1MNibXOL65y3n6hrS1zHWhqnV9ObD6VvNUiiZcSHNmDcSs5W00gk3d6u4w');        
        # INITIALISER UNE SESSION POUR METTRE LES INFOS QUE L'ON VEUT DANS LE SYSTEME DE PAYEMENT
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                'name' => 'T-shirt',
                ],
                'unit_amount' => 9000,
            ],
            'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),  # url absolue de la page success
            'cancel_url' => $this->generateUrl('error', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
      
         return new JsonResponse([ 'id' => $session->id ]);
      
    }  
    
    

    /**
     * @Route("/create-checkout-session3", name="checkout3")
     */

    public function checkout3(): Response
    {
        # clé privée de stripe
        \Stripe\Stripe::setApiKey('sk_test_51Hwb1rDqg9iWMtezpu1OkF1zDvHtg61jfJXPpIkU1MNibXOL65y3n6hrS1zHWhqnV9ObD6VvNUiiZcSHNmDcSs5W00gk3d6u4w');        
        # INITIALISER UNE SESSION POUR METTRE LES INFOS QUE L'ON VEUT DANS LE SYSTEME DE PAYEMENT
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                'name' => 'T-shirt',
                ],
                'unit_amount' => 12000,
            ],
            'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),  # url absolue de la page success
            'cancel_url' => $this->generateUrl('error', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
      
         return new JsonResponse([ 'id' => $session->id ]);
      
    }  
    
    
    
}


