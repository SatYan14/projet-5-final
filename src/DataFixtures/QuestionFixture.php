<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Question;
use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\QuestionLike;
use app\Entity\CommentLike;

class QuestionFixture extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        $users = [];
        $user = new User();
        $user->setEmail('test@symfony.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setDateCreation(new \DateTime)
            ->setUsername('Yan');

        $password = $this->encoder->encodePassword($user, 'password');
        $user->setPassword($password);

        $manager->persist($user);

        $users[] = $user;

        for ($a = 0; $a < 20; $a++) {
            $user = new User();
            $user->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($user, 'password'))
                ->setDateCreation(new \DateTime)
                ->setRoles(['ROLE_USER'])
                ->setUsername($faker->name);

            $manager->persist($user);
            $users[] = $user;
        }

        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence());

            $manager->persist($category);

            // Créer une dizaine de questions.

            for ($j = 1; $j <= mt_rand(8, 10); $j++) {
                $question = new Question();
                $question->setcontent($faker->sentence(2));
                $question->setCreatedAt($faker->dateTimeBetween('-2 months'));
                $question->setCategory($category);
                $question->setUser($faker->randomElement($users));

                $manager->persist($question);

                for ($k = 1; $k <= mt_rand(6, 9); $k++) {
                    $comment = new Comment();

                    $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';

                    $now = new \DateTime();
                    $interval = $now->diff($question->getCreatedAt());
                    $days = $interval->days;

                    $minimum = '-' . $days . ' days';

                    $comment->setDate($faker->dateTimeBetween($minimum));
                    $comment->setContent($content);
                    $comment->setUser($faker->randomElement($users));
                    $comment->setQuestion($question);


                    $manager->persist($comment);

                    for ($c = 0; $c <= mt_rand(0, 10); $c++) {
                        $like = new CommentLike();
                        $like->setComment($comment)
                            ->setUser($faker->randomElement($users));

                        $manager->persist($like);
                    }
                }
                for ($b = 0; $b <= mt_rand(0, 10); $b++) {
                    $like = new QuestionLike();
                    $like->setQuestion($question)
                        ->setUser($faker->randomElement($users));

                    $manager->persist($like);
                }
            }
        }
        $manager->flush();
    }
}
