<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Form\MessagesType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessagesController extends AbstractController
{
    /**
     * @Route("/messages", name="messages")
     */
    public function index(): Response
    {
        return $this->render('messages/index.html.twig', [
            'controller_name' => 'MessagesController',
        ]);
    }

    /**
     * @Route("/send", name="send")
     */
    public function send(Request $request, ManagerRegistry $managerRegi): Response
    {
        $message = new Messages();

        $form = $this->createForm(MessagesType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $message->setSender($this->getUser());

            $em = $managerRegi->getManager();
            $em->persist($message);
            $em->flush();

            $this->addFlash(
                'message',
                'Message envoyez avec success'
            );

            return $this->redirectToRoute('messages');
        }

        return $this->render('messages/send.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/received", name="received")
     */
    public function received(): Response
    {
        return $this->render('messages/received.html.twig');
    }

    /**
     * @Route("/read/{id}", name="read")
     */
    public function read(Messages $message, ManagerRegistry $managerRegi): Response
    {
        $message->setIsRead(true);

        $em = $managerRegi->getManager();
        $em->persist($message);
        $em->flush();
        return $this->render('messages/read.html.twig', compact("message"));
    }
}
