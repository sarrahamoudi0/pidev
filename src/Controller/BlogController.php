<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Rating;
use App\Repository\RatingRespository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

#[Route('/blog')]
class BlogController extends AbstractController
{

#[Route('/mobile/delete/{id}', name: 'mobile_blog_delete')]
public function deleteBlog(Blog $blog): JsonResponse
{
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($blog);
    $entityManager->flush();

    return new JsonResponse(['Deleted']);
}

    #[Route('/mobile/update/{id}', name: 'app_blog_update')]
public function updateblog(Request $request, Blog $blog): JsonResponse
{
    $title = $request->query->get('titre');
    $description = $request->query->get('description');
    $image = $request->query->get('image');
    $date = $request->query->get('date');

    // Update blog fields
    if ($title) {
        $blog->setTitle($title);
    }
    if ($description) {
        $blog->setDescription($description);
    }
    if ($image) {
        $blog->setImage($image);
    }
    if ($date) {
        $blog->setDate(new \DateTime($date));
    }

    // Persist updated entity
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($blog);
    $entityManager->flush();

    // Return updated entity
    return new JsonResponse(["Updated"
    ]);
}

    #[Route('/mobile/create', name: 'app_blog_create')]
    public function create(Request $request): JsonResponse
    {
        $title = $request->query->get('titre');
        $description = $request->query->get('description');
        $image = $request->query->get('image');
        $date = $request->query->get('date');
    
        // Validate required fields
        if (!$title || !$description || !$image || !$date) {
            return new JsonResponse(['error' => 'Missing required fields'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $entityManager = $this->getDoctrine()->getManager();
    
        $blog = new Blog();
        $blog->setTitle($title);
        $blog->setDescription($description);
        $blog->setImage($image);
        $blog->setDate(new \DateTime($date));
    
        $entityManager->persist($blog);
        $entityManager->flush();
    
        return new JsonResponse([
            'id' => $blog->getId(),
            'title' => $blog->getTitle(),
            'description' => $blog->getDescription(),
            'image' => $blog->getImage(),
            'date' => $blog->getDate()->format('Y-m-d'),
        ]);
    }

    #[Route('/mobile/show', name: 'mobile_show')]
   function showblog(BlogRepository $blogRepository): JsonResponse
    {
        $blogs = $blogRepository->findAll();
        $blogArray = [];
        foreach ($blogs as $blog) {
            $blogArray[] = [
                'id' => $blog->getId(),
                'title' => $blog->getTitle(),
                'description' => $blog->getDescription(),
                'image' => $blog->getImage(),
                'date' => $blog->getDate()->format('Y-m-d'),
            ];
        }
        return new JsonResponse($blogArray);
    }









    #[Route('/', name: 'app_blog_index', methods: ['GET'])]
    public function index(BlogRepository $blogRepository): Response
    {
        return $this->render('blog/index.html.twig', [
            'blogs' => $blogRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BlogRepository $blogRepository): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form['image']->getData();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $image->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $image->move(
                    $this->getParameter('image_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // Update the 'image' property to store the image file name
            // instead of its contents
            $blog->setImage($fileName);

            // Persist the blog entity
            $blogRepository->add($blog, true);
            return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_blog_show', methods: ['GET'])]
    public function show(Blog $blog): Response
    {
        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blog $blog, BlogRepository $blogRepository): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blogRepository->add($blog, true);

            return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_blog_delete', methods: ['POST'])]
    public function delete(Request $request, Blog $blog, BlogRepository $blogRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            $blogRepository->remove($blog, true);
        }

        return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
    }

    
    #[Route('/mobile/rating', name: 'mobile_rating')]
    public function rating(Request $request): Response
    {          
        
        $idblog = $request->query->get('idblog');
        $iduser = $request->query->get('iduser');
        $ratingValue = $request->query->get('rating');

        $rating = new Rating();

        $rating->setIdblog($idblog);
    
        $rating->setIduser($iduser);

        $rating->setRating($ratingValue);

        $em=$this->getDoctrine()->getManager();

        $em->persist($rating);

        $em->flush();

        $serializer = new Serializer([new ObjectNormalizer()]);


        $formatted = $serializer->normalize("rating");

        return new JsonResponse($formatted);
      
    } 
    #[Route('/mobile/check_rating', name: 'mobile_check_rating')]
    public function checkRating(Request $request, RatingRespository $a): Response
    {
        $iduser = $request->query->get('iduser');
        $entityManager = $this->getDoctrine()->getManager();

        $existingRatings = $a->findBy([
            'iduser' => $iduser,
        ]);
        
        if (!empty($existingRatings)) {
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = [];
        
            foreach ($existingRatings as $rating) {
                $formatted[] = $serializer->normalize([
                    'idblog' => $rating->getIdblog(),
                    'rating' => $rating->getRating(),
                    'iduser' => $iduser,
                ]);
            }
        
            return new JsonResponse($formatted);
        } else {
            return new JsonResponse([]);
        }
    }


#[Route('/mobile/updaterating', name: 'mobile_updaterating')]
public function updaterating(Request $request, RatingRespository $a): Response
{          
    $idblog = $request->query->get('idblog');
    $iduser = $request->query->get('iduser');
    $ratingValue = $request->query->get('rating');

    $existingRating = $a->findOneBy([
        'idblog' => $idblog,
        'iduser' => $iduser,
    ]);

    if ($existingRating !== null) {
        // Update existing rating
        $existingRating->setRating($ratingValue);

        $em = $this->getDoctrine()->getManager();
        $em->persist($existingRating);
        $em->flush();
    } else {
        // Create new rating
        $rating = new Rating();

        $rating->setIdblog($idblog);
        $rating->setIduser($iduser);
        $rating->setRating($ratingValue);

        $em = $this->getDoctrine()->getManager();
        $em->persist($rating);
        $em->flush();
    }

    $serializer = new Serializer([new ObjectNormalizer()]);
    $formatted = $serializer->normalize("rating");

    return new JsonResponse($formatted);  
}  
}
