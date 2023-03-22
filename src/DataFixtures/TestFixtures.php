<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Emprunt;
use App\Entity\Emprunteur;
use App\Entity\Livre;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class TestFixtures extends Fixture
{
    private $doctrine;
    private $faker;
    private $hasher;
    private $manager;

    public function __construct(ManagerRegistry $doctrine, UserPasswordHasherInterface $hasher)
    {
        $this->doctrine = $doctrine;
        $this->faker = FakerFactory::create('fr_FR');
        $this->hasher = $hasher;

    }

public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;    
        $this->loadUser();
        $this->loadAuteur();
        $this->loadLivre();
        $this->loadEmprunteur();
        $this->loadEmprunt();
    }

    public function loadUser(): void
    {
        $datas = [
            [
                'email' => 'admin@exemple.com',
                'roles' => ["ROLE_ADMIN"],
                'password' => '123',
                'enabled' => true
            ],
            [
                'email' => 'foo.foo@exemple.com',
                'roles' => ["ROLE_USER"],
                'password' => '123',
                'enabled' => true
            ],
            [
                'email' => 'bar.bar@exemple.com',
                'roles' => ["ROLE_USER"],
                'password' => '123',
                'enabled' => false
            ],
            [
                'email' => 'baz.baz@exemple.com',
                'roles' => ["ROLE_USER"],
                'password' => '123',
                'enabled' => true
            ],
        ];
    
        foreach ($datas as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles($data['roles']);
            $password = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($password);
            $user->setEnabled($data['enabled']);

            $this->manager->persist($user);
        };

        for ($i = 0; $i < 100; $i++) {
            
            $user = new User();
            $user->setEmail($this->faker->email());
            $user->setRoles(['ROLE_USER']);
            $password = $this->hasher->hashPassword($user, '123');
            $user->setPassword($password);
            $user->setEmail($this->faker->email());
            $user->setEnabled($this->faker->boolean());


            $this->manager->persist($user);
        }

        $this->manager->flush();
    }


    public function loadAuteur(): void
    {
        $datas = [
            [
                'nom' => 'auteur inconnu',
                'prenom' => ' ',
            ],
            [
                'nom' => 'Cartier',
                'prenom' => 'Hugues',
            ],
            [
                'nom' => 'Lambert',
                'prenom' => 'Armand',
            ],
            [
                'nom' => 'Moitessier',
                'prenom' => 'Thomas',
            ],
        ];
    
        foreach ($datas as $data) {
            $auteur = new Auteur();
            $auteur->setNom($data['nom']);
            $auteur->setPrenom($data['prenom']);

            $this->manager->persist($auteur);
        };

        for ($i = 0; $i < 500; $i++) {
            
            $auteur = new Auteur();
            $auteur->setNom($this->faker->lastname());
            $auteur->setPrenom($this->faker->firstname());           

            $this->manager->persist($auteur);
        }

        $this->manager->flush();
    }


    public function loadLivre(): void
    {
        $repository = $this->manager->getRepository(Auteur::class);
        $auteur = $repository->findAll();

        $datas = [
            [
                'titre' => 'Lorem ipsum dolor sit amet',
                'annee_edition' => 2010,
                'nombre_pages' => 100,
                'code_isbn' => '9785786930024',
                'auteur_id' => $auteur[0]
            ],
            [
                'titre' => 'Consectetur adipiscing elit',
                'annee_edition' => 2011,
                'nombre_pages' => 150,
                'code_isbn' => '9783817260935',
                'auteur_id' => $auteur[1]
            ],
            [
                'titre' => 'Mihi quidem Antiochum',
                'annee_edition' => 2012,
                'nombre_pages' => 200,
                'code_isbn' => '9782020493727',
                'auteur_id' => $auteur[2]
            ],
            [
                'titre' => 'Quem audis satis belle',
                'annee_edition' => 2013,
                'nombre_pages' => 250,
                'code_isbn' => '9794059561353',
                'auteur_id' => $auteur[3]
            ],
        ];
    
        foreach ($datas as $data) {

            $livre = new Livre();
            $livre->setTitre($data['titre']);
            $livre->setAnneeEdition($data['annee_edition']);
            $livre->setNombrePages($data['nombre_pages']);
            $livre->setCodeIsbn($data['code_isbn']);
            $livre->setAuteur($data['auteur_id']);

            $this->manager->persist($livre);
        };

        for ($i = 0; $i < 1000; $i++) {
            $this->faker->sentence();
            
            $livre = new Livre();
            $livre->setTitre($this->faker->sentence(3));
            $livre->setAnneeEdition($this->faker->numberBetween(2000, 2023));
            $livre->setNombrePages($this->faker->numberBetween(50, 500));
            $livre->setCodeIsbn($this->faker->numerify('#############'));
            $livre->setAuteur($this->faker->randomElement($auteur));

            $this->manager->persist($livre);
        }

        $this->manager->flush();
    }


    public function loadEmprunteur(): void
    {

        $repository = $this->manager->getRepository(User::class);
        $user = $repository->findAll();

        $datas = [
            [
                'nom' => 'Foo',
                'prenom' => 'Foo',
                'tel' => '123456789',
                'user_id' => $user[1]
            ],
            [
                'nom' => 'Bar',
                'prenom' => 'Bar',
                'tel' => '123456789',
                'user_id' => $user[2]
            ],
            [
                'nom' => 'Baz',
                'prenom' => 'Baz',
                'tel' => '123456789',
                'user_id' => $user[3]
            ],
        ];
    
        foreach ($datas as $data) {

            $emprunteur = new Emprunteur();
            $emprunteur->setNom($data['nom']);
            $emprunteur->setPrenom($data['prenom']);
            $emprunteur->setTel($data['tel']);
            $emprunteur->setUser($data['user_id']);

            $this->manager->persist($emprunteur);
        };

        $this->manager->flush();
    }


    public function loadEmprunt(): void
    {

        $repository = $this->manager->getRepository(Emprunteur::class);
        $emprunteur = $repository->findAll();

        $repository = $this->manager->getRepository(Livre::class);
        $livre = $repository->findAll();

        $datas = [
            [
                'date_emprunt' => DateTime::createFromFormat('Y-m-d H:i:s', '2020-02-01 10:00:00'),
                'date_retour' => DateTime::createFromFormat('Y-m-d H:i:s', '2020-03-01 10:00:00'),
                'emprunteur_id' => $emprunteur[0],
                'livre_id' => $livre[0]
            ],
            [
                'date_emprunt' => DateTime::createFromFormat('Y-m-d H:i:s', '2020-03-01 10:00:00'),
                'date_retour' => DateTime::createFromFormat('Y-m-d H:i:s', '2020-04-01 10:00:00'),
                'emprunteur_id' => $emprunteur[1],
                'livre_id' => $livre[1]
            ],
            [
                'date_emprunt' => DateTime::createFromFormat('Y-m-d H:i:s', '2020-04-01 10:00:00'),
                'date_retour' => null,
                'emprunteur_id' => $emprunteur[2],
                'livre_id' => $livre[2]
            ],
        ];
    
        foreach ($datas as $data) {

            $emprunt = new Emprunt();
            $emprunt->setDateEmprunt($data['date_emprunt']);
            $emprunt->setDateRetour($data['date_retour']);
            $emprunt->setEmprunteur($data['emprunteur_id']);
            $emprunt->setLivre($data['livre_id']);

            $this->manager->persist($emprunt);
        };

        $this->manager->flush();
    }
}