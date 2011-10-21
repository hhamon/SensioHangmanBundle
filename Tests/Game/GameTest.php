<?php

namespace Sensio\Bundle\HangmanBundle\Tests\Game;

use Sensio\Bundle\HangmanBundle\Game\Game;

class GameTest extends \PHPUnit_Framework_TestCase
{
    public function testGameIsWon()
    {
        $game = new Game('foo', 10);
        $game->tryLetter('o');
        $game->tryLetter('f');

        $this->assertEquals(array('f', 'o', 'o'), $game->getHiddenWord());
        $this->assertTrue($game->isWon());
    }
}