<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use Doctrine\ORM\EntityManagerInterface;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VideoController extends AbstractController
{
    #[Route('/video', name: 'app_video', methods: ['GET', 'POST'])]
    public function index(Request $request, Recaptcha3Validator $recaptcha3Validator, EntityManagerInterface $entityManager): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($recaptcha3Validator->getLastResponse()->getScore() >= 0.5) {
                $entityManager->persist($video);
                $entityManager->flush();
                return $this->redirectToRoute('app_home');
            }
            // C'est un robot...
        }

        return $this->render('video/index.html.twig', [
            'form' => $form,
        ]);
    }
}
