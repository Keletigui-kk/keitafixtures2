<?php
namespace App\Controller;
use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{


     /** 
     *@var PostRepository
     */
    private $repository;
    public function __construct(PostRepository $repo, EntityManagerInterface $em){
        $this->repository = $repo; // initialisation
        $this->em = $em; //initialisation
    }

    /**
     * @Route("/", name="home")
     */
    // public function index(): Response
    // {
    //     return $this->render('cv/index.html.twig', [
    //         'controller_name' => 'CvController',
    //     ]);
    // }

    public function index(): Response
    {   
        // GESTION post
        $post = new Post();
        $post1 = new Post();
        $post2 = new Post();
        $em = $this->getDoctrine()->getManager();

        $post = $em ->getRepository("App\Entity\post")->findBy(
            array('section' => 'content'), # critere
            array('createdAt' => 'DESC'), # tri
            6        # LIMIT
        );

        $post1 = $em ->getRepository("App\Entity\post")->findBy(
            array('section' => 'gauche'), # critere
            array('createdAt' => 'DESC'), # tri
            6        # LIMIT
        );
        
        $post2 = $em ->getRepository("App\Entity\post")->findBy(
            array('section' => 'droite'), # critere
            array('createdAt' => 'DESC'), # tri
            6        # LIMIT
        );
        return $this->render('home/index.html.twig', [
            'post' => $post, 'post1'=>$post1, 'post2'=>$post2
        ]);
        
    }
    

     /**
     * @Route("/posts/{id}", name="show_post")
     */
    public function show(Post $post1, PostRepository $repo, $id,Request $request, EntityManagerInterface $em)
    {

          # on crée notre formulaire
        $comment = new Comment();  # on exententie un nouveau commentaire

         # on utilise createForm maintenant comme on l'a crée par la commande
        $form = $this->createForm(CommentType::class, $comment);

         # on peut créer tous ces champs avec la commande: symfony console:make form: il cree un fichier CommentType.php
        $comment->setCreatedAt(new \DateTime());

        # on gère le post
        $comment->setPost($post1);  # on lui donne comme argument l'objet posts
          # on relie le formulaire
        $form->handleRequest($request);  # pour recuper les données du formulaire; ça ca automatiquement alimenter l'objet comment via le formulaire
          # on verifie lorsque le formulaire est soumis
        if($form->isSubmitted()&& $form->isValid()){  # on verifie le formulaire à été soumis et s'il est valide
            # on persiste l'objet comment
            $em->persist($comment);
            # on fait un flush de la $em
            $em->flush();
            //    # on fait un vardump die de commment pour si tout est ok
            //    dd($comment);
           }
       
        $em = $this->getDoctrine()->getManager();
    
        $post1 = $em ->getRepository("App\Entity\post")->findBy(
            array('section' => 'gauche'), # critere
            array('createdAt' => 'DESC'), # tri
            6        # LIMIT
        );
        
        $post2 = $em ->getRepository("App\Entity\post")->findBy(
            array('section' => 'droite'), # critere
            array('createdAt' => 'DESC'), # tri
            6        # LIMIT
        );
        

        $post = $repo->find($id);
        return $this->render('home/post.html.twig', [
            'post' => $post, 'post1'=>$post1, 'post2'=>$post2,
             # on transmet le formulaire à la vue
             'form' => $form->createView() 
        ]);
    }
}
