<?php

namespace Sensio\Bundle\HangmanBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Sensio\Bundle\HangmanBundle\Game\Dictionary;
use Sensio\Bundle\HangmanBundle\Game\Game;

class GameHangmanCommand extends ContainerAwareCommand
{
    private $game;
    private $word;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->dictionary = new Dictionary();
        $this->dictionary
            ->addWord('program')
            ->addWord('speaker')
            ->addWord('symfony')
            ->addWord('business')
            ->addWord('software')
            ->addWord('hardware')
            ->addWord('algorithm')
            ->addWord('framework')
            ->addWord('developer')
        ;
    }

    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('length', InputArgument::REQUIRED, 'The length of the word to guess'),
                new InputOption('max-attempts', null, InputOption::VALUE_OPTIONAL, 'Max number of attempts', 10),
            ))
            ->setName('game:hangman')
            ->setDescription('Play the famous hangman game from the CLI')
            ->setHelp(<<<EOF
The <info>game:hangman</info> command starts a new game of the
famous hangman game:

<info>game:hangman 8</info>

Try to guess the hidden <comment>word</comment> whose length is 
<comment>8</comment> before you reach the maximum number of 
<comment>attempts</comment>.

You can also configure the maximum number of attempts
with the <info>--max-attempts</info> option:

<info>game:hangman 8 --max-attempts=5</info>
EOF
            )
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        // Read the input
        $length = $input->getArgument('length');
        $attempts = $input->getOption('max-attempts');
        $dialog = $this->getHelperSet()->get('dialog');

        $word = $this->dictionary->getRandomWord($length);
        $this->game = new Game($word, $attempts);

        // Write the output
        $this->writeIntro($output, 'Welcome in the Hangman Game');
        $this->writeInfo($output, sprintf('You have %u attempts to guess the hidden word.', $attempts));
        $this->writeInfo($output, implode(' ', $this->game->getHiddenWord()));

        do {
            if ($letter = $dialog->ask($output, 'Type a letter... ')) {
                $this->game->tryLetter($letter);
                $this->writeInfo($output, implode(' ', $this->game->getHiddenWord()));
            }

            if (!$letter && $word = $dialog->ask($output, 'Try a word... ')) {
                $this->game->tryWord($word);
            }
        } while (!$this->game->isOver());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->game->isWon()) {
            $this->writeInfo($output, sprintf('Congratulations, you won and guessed the word "%s" in %u attempts.', $this->game->getWord(), $this->game->getAttempts()));
        } else {
            $this->writeError($output, sprintf('Oops, you\'ve been hanged! The word to guess was "%s".', $this->game->getWord()));
        }
    }

    private function writeError(OutputInterface $output, $message)
    {
        return $this->writeMessage($output, $message, 'error');
    }

    private function writeInfo(OutputInterface $output, $message)
    {
        return $this->writeMessage($output, $message, 'info');
    }

    private function writeIntro(OutputInterface $output, $message)
    {
        return $this->writeMessage($output, $message, 'bg=blue;fg=white');
    }

    private function writeMessage(OutputInterface $output, $message, $style)
    {
        $formatter = $this->getHelperSet()->get('formatter');
        $message = $formatter->formatBlock($message, $style, true);

        $output->writeln(array('', $message));
    }
}