<?php


namespace APP\tests;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;
use DateTime;

class dateTest extends TestCase {

    public function testGetPublishedAt() {
        $formation = new Formation();
        $formation->setPublishedAt(new \DateTime("2023-09-25 17:00:12"));
        $this->assertEquals("25/09/2023", $formation->getPublishedAtString());
    }

    // Cette méthode ne fonctionne pas car 2023-09-27 est différent de 25/09/2023

    public function testNonValidGetPublishedAt() {
        $formation = new Formation();
        $formation->setPublishedAt(new \DateTime("2023-09-27 17:00:12"));
        $this->assertEquals("25/09/2023", $formation->getPublishedAtString());
    }
}

