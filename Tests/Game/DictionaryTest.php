<?php

namespace Sensio\Bundle\HangmanBundle\Tests\Game;

use Sensio\Bundle\HangmanBundle\Game\Dictionary;

class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    private $dictionary;

    public function setUp()
    {
        $this->dictionary = new Dictionary();
        $this->dictionary->addWord('symfony');   // 7 letters
        $this->dictionary->addWord('software');  // 8 letters
        $this->dictionary->addWord('hardware');  // 8 letters
        $this->dictionary->addWord('developer'); // 9 letters
    }

    public function tearDown()
    {
        $this->dictionary = null;
    }

    public function testCount()
    {
        $this->assertEquals(4, $this->dictionary->count());
    }

    /**
     * @dataProvider provideInvalidWords
     *
     */
    public function testAddWordWithInvalidWords($word)
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->dictionary->addWord($word);
    }

    public function testWordIsNotAddedTwice()
    {
        $this->dictionary->addWord('foo');
        $this->dictionary->addWord('foo');
        $this->assertEquals(5, $this->dictionary->count());
    }

    
    public function testGetRandomWord()
    {
        $word = $this->dictionary->getRandomWord(8);
        $this->assertContains($word, array('software', 'hardware'));
    }

    /**
     * @dataProvider provideInvalidLengths
     *
     */
    public function testGetRandomWordThrowsException($length)
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->dictionary->getRandomWord($length);
    }

    public function provideInvalidLengths()
    {
        return array(
            array(6),
            array(10),
        );
    }

    public function provideInvalidWords()
    {
        return array(
            array('élève'),
            array('un chef'),
            array('12345'),
            array('l33t'),
        );
    }
}