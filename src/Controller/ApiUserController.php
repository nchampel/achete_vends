<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\World;
use App\Repository\ConstantRepository;
use App\Repository\WorldDataRepository;
use App\Repository\WorldRepository;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class ApiUserController extends AbstractController
{
    // #[Route('/api/user', name: 'app_api_user')]
    // public function index(): Response
    // {
    //     return $this->render('api_user/index.html.twig', [
    //         'controller_name' => 'ApiUserController',
    //     ]);
    // }
    #[Route('/api/me', name: 'api_me')]
    public function me(): Response|JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        
        // if(!($user)){
        //     return $this->redirectToRoute('app_login');
        // }
        $userData = $user->getProfileData();

        return $this->json([
                // 'message' => 'JWT valide !',
                'user' => $userData,
            ]);

        // $jwt= "token";

        // if($jwt == "token"){
        //     return $this->json([
        //         // 'message' => 'JWT valide !',
        //         'user' => $userData,
        //     ]);
        // } else {

        //     return $this->render('home/index.html.twig', [
        //         // 'controller_name' => 'HomeController',
        //         'user' => $userData,
        //     ]);
        // }

    }
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, 
    EntityManagerInterface $entityManager, ConstantRepository $repo, WorldRepository $worldRepository, WorldDataRepository $wdRepo, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        $authorization = $repo->findOneBy(["name" => "inscription", "value" => 1]);
        if(!$authorization){
            // return $this->redirectToRoute('app_register_error');
            return $this->json(['status_code' => 403, 'message' => "Inscription interdite par l'administrateur"], 403);
        }

        // Récupération du JSON envoyé par Flutter
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json([
                'status_code' => 400,
                'message' => 'JSON invalide'
            ], 400);
        }

        $user = new User();

        $pseudo = trim($data['pseudo'] ?? '');
        $password = $data['password'] ?? '';

        // Validation des données
        if ($pseudo === '' || $password === '') {
            return $this->json([
                'status_code' => 400,
                'message' => 'Le pseudo et le mot de passe sont obligatoires'
            ], 400);
        }

        if (strlen($pseudo) < 3) {
            return $this->json([
                'status_code' => 400,
                'message' => 'Le pseudo doit contenir au moins 3 caractères'
            ], 400);
        }

        if (strlen($password) < 6) {
            return $this->json([
                'status_code' => 400,
                'message' => 'Le mot de passe doit contenir au moins 6 caractères'
            ], 400);
        }

        $user->setPseudo($pseudo);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );
        
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
            return $this->json([
                'status_code' => 500,
                'message' => 'Le WorldData numéro 1 est introuvable.'
            ], 500);
            // throw new \RuntimeException('Le WorldData numéro 1 est introuvable.'); 
        }
        // $world = $worldRepository->findOneBy(["isUnlocked" => 1]);
        // encode the plain password
        
        // $user->setCurrentWorld($worldItem);
        $user->setCurrentWorld($currentWorld);

        $entityManager->persist($user);

    //     return $this->json([
    //     'status_code' => 201,
    //     'message' => 'Inscription réussie avant flush',
    //     'user' => [
    //         'id' => $user->getId(),
    //         'pseudo' => $user->getPseudo(),
    //         'user' => $user->getProfileData(),

    //     ]
    // ], 201);

        $entityManager->flush();

        $token = $JWTManager->create($user);

        // do anything else you need here, like send an email
        return $this->json([
        'status_code' => 201,
        'message' => 'Inscription réussie',
        'user' => [
            'id' => $user->getId(),
            'pseudo' => $user->getPseudo(),
            'user' => $user->getProfileData(),
            'token' => $token,
        ]
    ], 201);

        return $userAuthenticator->authenticateUser(
            $user,
            $authenticator,
            $request
        );
        

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
