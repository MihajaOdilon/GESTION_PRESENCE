<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use App\Entity\Prof;
use App\Form\ProfType;
use App\Repository\ProfRepository;
use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use App\Entity\Subject;
use App\Form\SubjectType;
use App\Repository\SubjectRepository;
use BaconQrCode\Encoder\QrCode;

#[IsGranted('ROLE_ADMIN')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    //STUDENTS

    #[Route('/students', name: 'students' , methods: ['GET'])]
    public function student(StudentRepository $studentRepository): Response
    {
        return $this->render('home/students/index.html.twig', [
            'students' => $studentRepository->findAll(),
        ]);
    }

    public function studentFilterByYear(EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(VotreEntite::class);

        // Utilisez la méthode `findBy` pour trier par la propriété `nom`
        $entitesTrie = $repository->findBy([], ['year' => 'ASC']);

        // Vous pouvez également utiliser 'DESC' pour un tri descendant

        // Faites quelque chose avec les entités triées
        // Par exemple, renvoyez-les dans une vue
        return $this->render('home/students/index.html.twig', [
            'entites' => $entitesTrie,
        ]);
    }

    #[Route('/students/add', name: 'students_add', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('students', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/students/add.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/students/edit', name: 'students_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Student $student, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('students', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/students/edit.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/student/{id}/delete', name: 'students_delete', methods: ['GET', 'POST'])]
    public function delete(Student $student, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $student->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($student);
            $entityManager->flush();
            $this->addFlash('success', 'L\'élément a été supprimé avec succès.');
        }

        return $this->redirectToRoute('students');
    }
    #[Route('/{id}/student/view', name: 'students_view', methods: ['GET'])]
    public function show(Student $student): Response
    {
        return $this->render('home/students/view.html.twig', [
            'student' => $student,
        ]);
    }
    //Subject
    #[Route('/profs', name: 'profs')]
    public function prof(ProfRepository $profRepository): Response
    {
        return $this->render('home/professeurs/index.html.twig', [
            'profs' => $profRepository->findAll(),
        ]);
    }

    #[Route('/profs/add', name: 'profs_add', methods: ['GET', 'POST'])]
    public function newProf(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prof = new Prof();
        $form = $this->createForm(ProfType::class, $prof);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($prof);
            $entityManager->flush();

            return $this->redirectToRoute('profs', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/professeurs/add.html.twig', [
            'prof' => $prof,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/profs/edit', name: 'profs_edit', methods: ['GET', 'POST'])]
    public function editProf(Request $request, Prof $prof, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProfType::class, $prof);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('profs', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/professeurs/edit.html.twig', [
            'prof' => $prof,
            'form' => $form,
        ]);
    }

    //ROOM
    #[Route('/rooms', name: 'rooms')]
    public function room(RoomRepository $roomRepository): Response
    {
        return $this->render('home/salles/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    #[Route('/rooms/add', name: 'rooms_add', methods: ['GET', 'POST'])]
    public function newRoom(Request $request, EntityManagerInterface $entityManager): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('rooms', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/salles/add.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/rooms/edit', name: 'rooms_edit', methods: ['GET', 'POST'])]
    public function editRoom(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('rooms', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/salles/edit.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }
    
   //SUBJECT
    #[Route('/subjects', name: 'subjects' , methods: ['GET'])]
    public function subject(SubjectRepository $subjectRepository): Response
    {
        return $this->render('home/subjects/index.html.twig', [
            'subjects' => $subjectRepository->findAll(),
        ]);
    }

    #[Route('/subjects/add', name: 'subjects_add', methods: ['GET', 'POST'])]
    public function newSubject(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subject = new Subject();
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subject);
            $entityManager->flush();

            return $this->redirectToRoute('subjects', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/subjects/add.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/subjects/edit', name: 'subject_edit', methods: ['GET', 'POST'])]
    public function editSubject(Request $request, Subject $subject, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('subjects', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/subjects/edit.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }
  //QRCODE
  #[Route('/qrcode', name: 'qrcode')]
    public function generateQRCode(): Response
    {
        $qrCode = new QrCode('Vos données personnalisées ici');

        // Générez le contenu binaire du QR code au format PNG
        $binaryContent = $qrCode->writeString();
        
        // Créez une réponse Symfony pour afficher le QR code dans un navigateur
        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        $response->setContent($binaryContent);
        
        // Retournez la réponse
        return $response;
    }
}
