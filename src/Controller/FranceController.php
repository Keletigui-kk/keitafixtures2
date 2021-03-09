<?php
 
namespace App\Controller;
 
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
 
class FranceController extends AbstractController
{
 
     /**
     *  @var postRepository
     */
 
    private $repository;
 
    public function __construct(PostRepository $repo, EntityManagerInterface $em)
    {
    $this->repository = $repo;//initialisation
    $this->em = $em;//initialisation
    }
 
    /**
     * @Route("/france", name="france")
     */
    
    public function cinema(): Response
    
    {
        $post = new post();
        $post1 = new post();
        $post2 = new post();
 
        $em = $this->getDoctrine()->getManager();
 
        $post = $em->getRepository("App\Entity\post")->findBy(
            array('categorie' => 'france'),
            array('createdAt' => 'DESC'),
            1, 0
        );
        $post1 = $em->getRepository("App\Entity\post")->findBy(
            array('categorie' => 'france'),
            array('createdAt' => 'DESC'),
            3, 1
        );
        $post2 = $em->getRepository("App\Entity\post")->findBy(
            array('categorie' => 'france'),
            array('createdAt' => 'DESC'),
            3, 4
        );
        return $this->render('france/index.html.twig', [
            'post' => $post,
            'post1' => $post1,
            'post2' => $post2
        ]); 
    }
 
    
}