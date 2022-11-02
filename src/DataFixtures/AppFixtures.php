<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Homepage;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository, ArticleRepository $articleRepository)
    {
        $this->slugger = $slugger;
        $this->passwordHasher = $userPasswordHasher;
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
    }

    private function curl_get_contents($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function load(ObjectManager $manager) : void
    {
        //Add users and admin
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@test.com',
                'password' => '123456',
                'roles' => ['ROLE_ADMIN'],
                'isBlocked' => false,
                'avatar' => 'https://static.thenounproject.com/png/139500-200.png',
            ],
            [
                'username' => 'user1',
                'email' => 'user1@test.com',
                'password' => '123456',
                'roles' => ['ROLE_INDIVIDUAL'],
                'isBlocked' => false,
                'avatar' => 'https://randomuser.me/api/portraits/men/'.rand(0, 99).'.jpg',
                'phone' => '0606060606',
            ],
            [
                'username' => 'user2',
                'email' => 'user2@test.com',
                'password' => '123456',
                'roles' => ['ROLE_INDIVIDUAL'],
                'isBlocked' => true,
                'phone' => '0606060606',
            ],
            [
                'noSiret' => '458675',
                'name' => 'Nike',
                'email' => 'pro1@test.com',
                'password' => '123456',
                'roles' => ['ROLE_PRO'],
                'isBlocked' => false,
                'avatar' => 'https://randomuser.me/api/portraits/women/'.rand(0, 99).'.jpg',
                'phone' => '0606060606',
            ],
            [
                'noSiret' => '326895',
                'name' => 'IKEA',
                'email' => 'pro2@test.com',
                'password' => '123456',
                'roles' => ['ROLE_PRO'],
                'isBlocked' => true,
                'avatar' => 'https://randomuser.me/api/portraits/men/'.rand(0, 99).'.jpg',
                'phone' => '0606060606',
            ],
            [
                'noSiret' => '48756',
                'name' => 'NORDIC',
                'email' => 'pro3@test.com',
                'password' => '123456',
                'roles' => ['ROLE_PRO'],
                'isBlocked' => false,
                'avatar' => 'https://randomuser.me/api/portraits/men/'.rand(0, 99).'.jpg',
                'phone' => '0606060606',
            ],
        ];
        //First delete old avatars
        $avatars_directory = 'public/uploads/images/avatars/';
        $files = scandir($avatars_directory);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && $file != 'default.jpg' && $file != 'default.png') {
                unlink($avatars_directory . $file);
            }
        }
        //Then add users
        foreach ($users as $user) {
            $user_object = new User();
            $user_object->setEmail($user['email']);
            $user_object->setPassword($this->passwordHasher->hashPassword($user_object, $user['password']));
            $user_object->setIsBlocked($user['isBlocked']);
            $user_object->setRoles($user['roles']);
            $user_object->setPhone($user['phone'] ?? null);
            if (isset($user['avatar'])) {
                $avatar = $this->curl_get_contents($user['avatar']);
                $avatar_name = md5($user['avatar']) . '.jpg';
                file_put_contents($avatars_directory . $avatar_name, $avatar);
                $user_object->setAvatar($avatar_name);
            }

            if ($user['roles'] == ['ROLE_INDIVIDUAL']) {
                $user_object->setUsername($user['username']);
            } elseif ($user['roles'] == ['ROLE_PRO']) {
                $user_object->setNoSiret($user['noSiret']);
                $user_object->setName($user['name']);
            }
            $manager->persist($user_object);
        }
        $manager->flush();


        //Add articles
        $articles = [
            ['Vêtement','Pantalon cargo','jpeg', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F59e39263-711c-443d-aec8-e358620e3dd7%2Fp1.jpeg?table=block&id=c8fb9aab-85e9-4db1-9c02-8f5ce50bcf57&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=450&userId=&cache=v2'],   
            ['Vêtement','Robe','png', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F58990fa0-e835-4976-b6fb-91684236cc5f%2FUntitled.png?table=block&id=9557aec7-d717-42c6-ba01-6f7fe31fa05c&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=450&userId=&cache=v2'],
            ['Vêtement','Pantalon','jpeg', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F6ca6bcde-0c6c-43ab-8011-3097a7e0ec43%2Fp2.jpeg?table=block&id=4bc44524-bcb4-4150-8346-73d5210efdac&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=370&userId=&cache=v2'],
            ['Vêtement','Veste jaune','png', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F0bb438a9-b2e9-4c2c-806d-16d6547a43e6%2FUntitled.png?table=block&id=dea30251-1d88-4937-8211-dc5652c53b11&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=1400&userId=&cache=v2'],
            ['Vêtement','T-shirt jaune','jpeg', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F39a7cd51-8a0f-4d07-9f70-462f0cd45e04%2Ft1.jpeg?table=block&id=e70ba314-2d4b-4791-b1be-fa71573db269&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=400&userId=&cache=v2'],
            ['Vêtement','Jean','png', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2Fd1d871b3-7ea3-4ad7-9203-e3c2c966e1f4%2FUntitled.png?table=block&id=501d908a-c92e-4486-a343-c5f97b5e8ab3&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=720&userId=&cache=v2'],
            ['Vêtement','T-shirt noir','jpeg', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F02eb68d3-38c3-4bba-8068-cdaf5b469f77%2Ft2.jpeg?table=block&id=26a44099-3a06-48bc-bafe-d9856a3f5acb&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=450&userId=&cache=v2'],
            ['Vêtement','T-shirt rouge','jpeg', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F6d6ab22a-3bba-4463-beae-6b511847399f%2Ft3.jpeg?table=block&id=877a3ad1-3ba9-4a6b-90eb-c775501d6cec&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=450&userId=&cache=v2'],
            ['Voiture', 'Peugeot Jaune', 'jpg', 'https://www.auto-moto.com/wp-content/uploads/sites/9/2022/02/01-peugeot-208-750x410.jpg'],
            ['Voiture', 'Renault Orange', 'jpg', 'https://www.challenges.fr/assets/img/2021/10/14/cover-r4x3w1000-61b78a7828736-27157-1601439-k2-k1-3696041-jpg.jpg'],
            ['Voiture', 'Audi Noir', 'jpg', 'https://static.cnews.fr/sites/default/files/styles/image_750_422/public/audi_62ea632803c1e.jpg?itok=w5M6aSss'],
            ['Voiture', 'Citroën', 'webp', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT59yvMSQz3e3HzUsiVY2NR_ZStZgEX8OKFDA&usqp=CAU'],
            ['Jardinage', 'Kit 3 outils à fleurs', 'jpg', 'https://images.truffaut.com/media/catalog/productcdn:///Articles/jpg/0567000/567019_001.jpg?width=700&height=700&store=fr&image-type=image'],
            ['Jardinage', 'Outils Jardinage Manche En Bois', 'webp', 'https://sc02.alicdn.com/kf/Hf259742da91e46e48d9c2f78ab6988bas/239720024/Hf259742da91e46e48d9c2f78ab6988bas.jpg_.webp'],
            ['Jardinage', 'Beche avec manche et Protection pied Jad jardin', 'jpg', 'https://www.provence-outillage.fr/data/images/beche-a-00163.800.jpg'],
            ['Mobilier', 'Table de repas extensible EDEN', 'jpg', 'https://www.mobilierdefrance.com/23874/table-de-repas-extensible-eden.jpg'],
            ['Mobilier', 'Fauteuil Scandinave en Tissu Gris', 'jpg', 'https://www.mobilier-deco.com/img/produit/thumbs/jenna-fauteuil-scandinave-en-tissu-gris-jenna-1.jpg'],
            ['Mobilier', 'Canapé scandinave d\'angle réversible convertible - Gris', 'jpg', 'https://bestmobilier.com/6954-home_default/nordic-canape-scandinave-d-angle-reversible-convertible-gris-clair.jpg'],
        ];
        //First delete old article images except default images
        $images_directory = 'public/uploads/images/articles/';
        $files = scandir($images_directory);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && $file != 'default.jpg' && $file != 'default.png') {
                unlink($images_directory . $file);
            }
        }
        //Then add articles and associated images and save them in database
        foreach ($articles as $article_info) {
            $article = new Article();
            // download image
            $image = file_get_contents($article_info[3]);
            $image_name = md5(uniqid()) . '.' . $article_info[2];
            $image_path = 'public/uploads/images/articles/' . $image_name;
            file_put_contents($image_path, $image);
            $article->setImgArticle($image_name);
            
            $article->setTitle($article_info[1]);
            // random price
            $article->setPrice(mt_rand(10, 100));
            // random date
            $article->setDateParution(new \DateTime('now - '.mt_rand(0, 100).' days'));
            $article->setUpdatedAt(new \DateTimeImmutable('now - '.mt_rand(0, 100).' days'));
            // random quantity
            $article->setQuantity(mt_rand(0, 100));
            $article->setCategory($article_info[0]);
            $article->setNegotiation(mt_rand(0, 1));
            $article->setUsed(mt_rand(0, 1));
            // Get only user with role ROLE_PRO
            $users = $this->userRepository->findByRole('ROLE_PRO');
            $article->setAuthor($users[mt_rand(0, count($users) - 1)]);
            $manager->persist($article);
        }

        $manager->flush();

        // Randomly favorite articles by users with role ROLE_INDIVIDUAL
        $users = $this->userRepository->findByRole('ROLE_INDIVIDUAL');
        $articles = $this->articleRepository->findAll();
        foreach ($users as $user) {
            $nb_articles_favorite = mt_rand(1, count($articles));
            for ($i = 0; $i < $nb_articles_favorite; $i++) {
                $user->addFavoriteArticle($articles[mt_rand(0, count($articles) - 1)]);
            }
            $manager->persist($user);
        }

        //Add the homepage entry
        $homepage = new Homepage();
        $homepage->setSections([
            'mostRecentArticles',
            'mostFavoriteArticles',
        ]);
        $manager->persist($homepage);


        $manager->flush();
    }
}
