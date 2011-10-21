<?php

namespace Sensio\Bundle\HangmanBundle\Game;

class Dictionary implements \Countable
{
    private $words;

    public function addWord($word)
    {
        if (!preg_match('/^[a-z]+$/i', $word)) {
            throw new \InvalidArgumentException(sprintf('The word "%s" contains invalid characters. A word must only contain alpha characters.', $word));
        }

        $length = strlen($word);

        if (isset($this->words[$length]) && !in_array($word, $this->words[$length])) {
            $this->words[$length][] = $word;
        } else {
            $this->words[$length] = array($word);
        }

        return $this;
    }

    public function count()
    {
        $count = 0;
        foreach ($this->words as $words) {
            $count += count($words);
        }

        return $count;
    }

    public function getRandomWord($length)
    {
        $lengths = array_keys($this->words);

        if (!in_array($length, $lengths)) {
            throw new \InvalidArgumentException(sprintf('The length "%s" must be an integer between %u and %u.', $length, min($lengths), max($lengths)));
        }

        $words = $this->words[$length];

        return $words[array_rand($words)];
    }
}