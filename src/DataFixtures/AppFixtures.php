<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Track;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $track = new Track();
        $track->setTitle('Track 1');
        $track->setArtist('Artist 1');
        $track->setDuration(340);
        $manager->persist($track);


        $track = new Track();
        $track->setTitle('Track 2');
        $track->setArtist('Artist 2');
        $track->setDuration(240);
        $manager->persist($track);


        $track = new Track();
        $track->setTitle('Track 3');
        $track->setArtist('Artist 3');
        $track->setDuration(180);
        $manager->persist($track);

        $track = new Track();
        $track->setTitle('Track 4');
        $track->setArtist('Artist 4');
        $track->setDuration(200);
        $manager->persist($track);

        $track = new Track();
        $track->setTitle('Track 5');
        $track->setArtist('Artist 5');
        $track->setDuration(220);
        $manager->persist($track);

        $manager->flush();
    }
}
