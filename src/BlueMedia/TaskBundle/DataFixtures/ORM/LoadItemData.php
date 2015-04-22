<?php

namespace BlueMedia\TaskBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use BlueMedia\TaskBundle\Entity\Item;

class LoadItemData implements FixtureInterface {

    public function load(ObjectManager $manager) {
        $item = new Item();
        $item->setName('Produkt 1');
        $item->setAmount(4);
        $manager->persist($item);

        $item = new Item();
        $item->setName('Produkt 2');
        $item->setAmount(12);
        $manager->persist($item);

        $item = new Item();
        $item->setName('Produkt 5');
        $item->setAmount(0);
        $manager->persist($item);

        $item = new Item();
        $item->setName('Produkt 7');
        $item->setAmount(6);
        $manager->persist($item);

        $item = new Item();
        $item->setName('Produkt 8');
        $item->setAmount(2);
        $manager->persist($item);

        $manager->flush();
    }

}
