<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\World;
use App\Form\RegistrationFormType;
use App\Repository\ConstantRepository;
use App\Repository\WorldDataRepository;
use App\Repository\WorldRepository;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
// use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/app/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, 
    EntityManagerInterface $entityManager, ConstantRepository $repo, WorldRepository $worldRepository, WorldDataRepository $wdRepo): Response
    {
        $authorization = $repo->findOneBy(["name" => "inscription", "value" => 1]);
        if(!$authorization){
            return $this->redirectToRoute('app_register_error');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Le monde de départ du nouvel utilisateur
            $currentWorld = null;
            $worlds = $wdRepo->findAll();
            foreach($worlds as $worldItem){
                $world = new World();
                if($worldItem->getNumber() == 1){
                    $world->setIsUnlocked(1);
                    $currentWorld = $world;
                } else {

                    $world->setIsUnlocked(0);
                }
                $world->setWorldData($worldItem);
                $world->setUser($user);
                $entityManager->persist($world);
            }
            // Vérification 
            if ($currentWorld === null) { 
                throw new \RuntimeException('Le WorldData numéro 1 est introuvable.'); 
            }
            // argent de départ
            $user->setMoney(100);
            // $world = $worldRepository->findOneBy(["isUnlocked" => 1]);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // $user->setCurrentWorld($worldItem);
            $user->setCurrentWorld($currentWorld);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    #[Route('/register/error', name: 'app_register_error')]
    public function registerError(): Response
    {
        return $this->render('registration/registerError.html.twig');
    }
}
