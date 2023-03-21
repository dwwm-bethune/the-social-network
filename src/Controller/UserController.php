<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\EditProfileType;
use App\Form\PostType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    #[Route('/membres', name: 'app_user')]
    #[IsGranted('ROLE_USER')]
    public function index(UserRepository $repository): Response
    {
        return $this->render('user/index.html.twig', [
            'members' => $repository->findAll(),
        ]);
    }

    #[Route('/membre/{username}', name: 'app_user_show')]
    public function show(Request $request, User $user, EntityManagerInterface $manager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreator($this->getUser());

            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute('app_user_show', ['username' => $this->getUser()->getUsername()]);
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/membre/profil/modifier', name: 'app_user_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EditProfileType::class, $user = $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Pseudo => Fio rella => fio-rella
            if (!$user->getUsername()) {
                $user->setUsername($slugger->slug($user->getFirstname())->lower());
            }

            /** @var UploadedFile $file */
            $file = $form->get('avatarFile')->getData();

            if ($file) {
                $publicDir = __DIR__.'/../../public';

                if ($user->getAvatar()) {
                    $filesystem = new Filesystem();
                    $filesystem->remove($publicDir.'/'.$user->getAvatar());
                }

                $file->move($publicDir.'/users', $avatar = $user->getUsername().'-'.uniqid().'.'.$file->guessExtension());
                $user->setAvatar('users/'.$avatar);
            }

            $manager->flush();

            return $this->redirectToRoute('app_user_show', ['username' => $user->getUsername()]);
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
        ]);
    }
}
