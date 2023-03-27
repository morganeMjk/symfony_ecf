<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Repository\AuteurRepository;
use App\Entity\Auteur;
use App\Repository\LivreRepository;
use App\Entity\Livre;
use App\Repository\EmprunteurRepository;
use App\Entity\Emprunteur;
use App\Repository\EmpruntRepository;
use App\Entity\Emprunt;
use Exception;
use DateTime;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/test')]
class TestController extends AbstractController
{
    #[Route('/user', name: 'app_test_user')]
    public function user(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(User::class);


        // - la liste complète de tous les utilisateurs (de la table `user`), triée par ordre alphabétique d'email
        $users = $repository->findAllUsers();
        dump($users);

        // - les données de l'utilisateur dont l'id est `1`
        $users = $repository->findUserById(1);
        dump($users);

        // - la liste des utilisateurs dont l'attribut `roles` contient le mot clé `ROLE_USER`, triée par ordre alphabétique d'email
        $users = $repository->findUserByRole();
        dump($users);

        
        exit();
    }


    #[Route('/livre', name: 'app_test_livre')]
    public function livre(ManagerRegistry $doctrine): Response
    {
        $livreRepository = $doctrine->getRepository(Livre::class);
        $em = $doctrine->getManager();


        // - la liste complète de tous les livres, triée par ordre alphabétique de titre
        $livre = $livreRepository->findAll();
        dump($livre);


        // - les données du livre dont l'id est `1`
        $livre = $livreRepository->findById();
        dump($livre);


        // - la liste des livres dont le titre contient le mot clé `lorem`, triée par ordre alphabétique de titre
        $livre = $livreRepository->findByKeyword();
        dump($livre);


        // Requêtes de création :
        //     - ajouter un nouveau livre
        //     - titre : Totum autem id externum
        //     - année d'édition : 2020
        //     - nombre de pages : 300
        //     - code ISBN : 9790412882714
        //      - auteur : Hugues Cartier (id `2`)
        $auteurRepository = $doctrine->getRepository(Auteur::class);
        $auteur = $auteurRepository->find(2);
        $livre = new Livre();
        $livre->setTitre('Totum autem id externum');
        $livre->setAnneeEdition(2020);
        $livre->setNombrePages(300);
        $livre->setCodeIsbn('9790412882714');
        $livre->setAuteur($auteur);
        $em->persist($livre);
        $em->flush();


        // Requêtes de mise à jour :
        //     - modifier le livre dont l'id est `2`
        //     - titre : Aperiendum est igitur
        $livre2 = $livreRepository->find(2);
        $livre2->setTitre('Aperiendum est igitur');
        $em->flush();
        dump($livre2);


        // Requêtes de suppression :
        //     - supprimer le livre dont l'id est `123`
        $tag123 = $livreRepository->find(123);
        try {
            $em->remove($tag123);
            $em->flush();
        } catch (Exception $e) {
            dump($e->getMessage());
            dump($e->getCode());
            dump($e->getFile());
            dump($e->getLine());
            dump($e->getTraceAsString());
        }
        dump($tag123); 


        exit();
    }


    #[Route('/emprunteur', name: 'app_test_emprunteur')]
    public function emprunteur(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Emprunteur::class);


        // - la liste complète des emprunteurs, triée par ordre alphabétique de nom et prénom
        $emprunteur = $repository->findAll();
        dump($emprunteur);


        // - les données de l'emprunteur qui est relié au user dont l'id est `3`
        $emprunteur = $repository->findByUserId();
        dump($emprunteur);


        // - la liste des emprunteurs dont le nom ou le prénom contient le mot clé `foo`, triée par ordre alphabétique de nom et prénom
        $emprunteur = $repository->findByKeyword();
        dump($emprunteur);


        exit();
    }


    #[Route('/emprunt', name: 'app_test_emprunt')]
    public function emprunt(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Emprunt::class);
        $em = $doctrine->getManager();


        // - la liste des 3 derniers emprunts au niveau chronologique, triée par ordre **décroissant** de date d'emprunt (le plus récent en premier)
        $emprunt = $repository->findLast();
        dump($emprunt);


        // - la liste des emprunts de l'emprunteur dont l'id est `2`, triée par ordre **croissant** de date d'emprunt (le plus ancien en premier)
        $emprunt = $repository->findByBorrowerId();
        dump($emprunt);

        
        // - la liste des emprunts du livre dont l'id est `3`, triée par ordre **décroissant** de date d'emprunt (le plus récent en premier)
        $emprunt = $repository->findByBookId();
        dump($emprunt);


        // - la liste des emprunts qui n'ont pas encore été retournés (c-à-d dont la date de retour est nulle), triée par ordre **croissant** de date d'emprunt (le plus ancien en premier)
        $emprunt = $repository->findNotReturn();
        dump($emprunt);


        // Requêtes de création :
        //     - ajouter un nouvel emprunt
        //     - date d'emprunt : 01/12/2020 à 16h00
        //     - date de retour : aucune date
        //     - emprunteur : foo foo (id `1`)
        //     - livre : Lorem ipsum dolor sit amet (id `1`)
        $emprunteurRepository = $doctrine->getRepository(Emprunteur::class);
        $emprunteur1 = $emprunteurRepository->find(1);
        $livreRepository = $doctrine->getRepository(Livre::class);
        $livre1 = $livreRepository->find(1);
        $emprunt = new Emprunt();
        $emprunt->setDateEmprunt(DateTime::createFromFormat('Y-m-d H:i:s', '2020-12-01 16:00:00'));
        $emprunt->setDateRetour(null);
        $emprunt->setEmprunteur($emprunteur1);
        $emprunt->setLivre($livre1);
        $em->persist($emprunt);
        $em->flush();


        // Requêtes de mise à jour :
        //     - modifier l'emprunt dont l'id est `3`
        //     - date de retour : 01/05/2020 à 10h00
        $emprunt3 = $repository->find(3);
        $emprunt3->setDateRetour(DateTime::createFromFormat('Y-m-d H:i:s', '2020-05-01 10:00:00'));
        $em->flush();
        dump($emprunt3);


        exit();
    }
}