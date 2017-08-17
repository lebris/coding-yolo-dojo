<?php

namespace Dojo\Console;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CashMachine extends Command
{
    protected function configure()
    {
        $this->setName('cash:machine')
             ->setDescription('Say hello world');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $productPrices = [
            'Apples' => 100,
            'Bananas' => 150,
            'Cherries' => 75,
        ];

        $totalPrice = 0;

        $helper = new QuestionHelper();
        while (true) {
            $question = new Question('Please enter the product name : ', '');
            $product = $helper->ask($input, $output, $question);

            if ($product === '') {
                break;
            }

            if(! array_key_exists($product, $productPrices))
            {
                $output->writeln('No product found');
                continue;
            }

            $totalPrice += $productPrices[$product];

            $output->writeln($totalPrice);
        }

    }
}
