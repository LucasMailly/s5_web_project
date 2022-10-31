<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Homepage;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository)
    {
        $this->slugger = $slugger;
        $this->passwordHasher = $userPasswordHasher;
        $this->userRepository = $userRepository;
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
            ],
            [
                'username' => 'user2',
                'email' => 'user2@test.com',
                'password' => '123456',
                'roles' => ['ROLE_INDIVIDUAL'],
                'isBlocked' => true,
            ],
            [
                'noSiret' => '458675',
                'name' => 'Amazon',
                'email' => 'pro1@test.com',
                'password' => '123456',
                'roles' => ['ROLE_PRO'],
                'isBlocked' => false,
                'avatar' => 'https://randomuser.me/api/portraits/women/'.rand(0, 99).'.jpg',
            ],
            [
                'noSiret' => '326895',
                'name' => 'Google',
                'email' => 'pro2@test.com',
                'password' => '123456',
                'roles' => ['ROLE_PRO'],
                'isBlocked' => true,
                'avatar' => 'https://randomuser.me/api/portraits/men/'.rand(0, 99).'.jpg',
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
        $images = [
            'Pantalon cargo' => ['jpeg', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F59e39263-711c-443d-aec8-e358620e3dd7%2Fp1.jpeg?table=block&id=c8fb9aab-85e9-4db1-9c02-8f5ce50bcf57&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=450&userId=&cache=v2'],   
            'Robe' => ['png', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F58990fa0-e835-4976-b6fb-91684236cc5f%2FUntitled.png?table=block&id=9557aec7-d717-42c6-ba01-6f7fe31fa05c&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=450&userId=&cache=v2'],
            'Pantalon' => ['jpeg', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F6ca6bcde-0c6c-43ab-8011-3097a7e0ec43%2Fp2.jpeg?table=block&id=4bc44524-bcb4-4150-8346-73d5210efdac&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=370&userId=&cache=v2'],
            'Veste jaune' => ['png', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F0bb438a9-b2e9-4c2c-806d-16d6547a43e6%2FUntitled.png?table=block&id=dea30251-1d88-4937-8211-dc5652c53b11&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=1400&userId=&cache=v2'],
            'T-shirt jaune' => ['jpeg', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F39a7cd51-8a0f-4d07-9f70-462f0cd45e04%2Ft1.jpeg?table=block&id=e70ba314-2d4b-4791-b1be-fa71573db269&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=400&userId=&cache=v2'],
            'Jean' => ['png', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2Fd1d871b3-7ea3-4ad7-9203-e3c2c966e1f4%2FUntitled.png?table=block&id=501d908a-c92e-4486-a343-c5f97b5e8ab3&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=720&userId=&cache=v2'],
            'T-shirt noir' => ['jpeg', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F02eb68d3-38c3-4bba-8068-cdaf5b469f77%2Ft2.jpeg?table=block&id=26a44099-3a06-48bc-bafe-d9856a3f5acb&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=450&userId=&cache=v2'],
            'T-shirt rouge' => ['jpeg', 'https://rowan-comfort-e0c.notion.site/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F6d6ab22a-3bba-4463-beae-6b511847399f%2Ft3.jpeg?table=block&id=877a3ad1-3ba9-4a6b-90eb-c775501d6cec&spaceId=ab3e3a4d-9b9b-467c-819b-cfd55fc07fe0&width=450&userId=&cache=v2'],
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
        foreach ($images as $libelle => $image_url) {
            $article = new Article();
            // download image
            $image = file_get_contents($image_url[1]);
            $image_name = md5(uniqid()) . '.' . $image_url[0];
            $image_path = 'public/uploads/images/articles/' . $image_name;
            file_put_contents($image_path, $image);
            $article->setImgArticle($image_name);
            
            $article->setLibelle($libelle);
            // random price
            $article->setPrice(mt_rand(10, 100));
            // random date
            $article->setDateParution(new \DateTime('now - '.mt_rand(0, 100).' days'));
            $article->setUpdatedAt(new \DateTimeImmutable('now - '.mt_rand(0, 100).' days'));
            // random quantity
            $article->setQuantity(mt_rand(0, 100));
            // first part of $libelle is the category
            $article->setCategory(explode(' ', $libelle)[0]);
            $article->setNegotiation(mt_rand(0, 1));
            $article->setOpportunity(mt_rand(0, 1));
            $users = $this->userRepository->findAll();
            $article->setAuthor($users[mt_rand(0, count($users) - 1)]);
            $manager->persist($article);
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