<?php

namespace Dojo\Console;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CashMachine extends Command
{
    private
        $productPrices;

    public function __construct()
    {
        parent::__construct();

        $this->productPrices = [
            'Apples' => 100,
            'Bananas' => 150,
            'Cherries' => 75,
        ];
    }

    protected function configure()
    {
        $this->setName('cash:machine')
             ->setDescription('Say hello world');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = new QuestionHelper();
        $list = [];

        while (true)
        {
            $question = new Question(sprintf('Please enter the product name (%s): ', implode(' , ', array_keys($this->productPrices))), '');
            $products = explode(',', $helper->ask($input, $output, $question));

            foreach($products as $product)
            {
                $product = trim($product);

                if ($product === '') {
                    break 2;
                }

                if(! array_key_exists($product, $this->productPrices))
                {
                    $output->writeln('Product ' . $product . ' not found');
                    continue;
                }

                if(! array_key_exists($product, $list))
                {
                    $list[$product] = 0;
                }
                $list[$product] += 1;

            }

            $output->writeln($this->computeTotal($list));
        }
    }

    private function computeTotal(array $list)
    {
        $total = 0;
        foreach($list as $item => $qte)
        {
            $total += $this->computePrice($item, $qte);
        }

        return $total;
    }

    private function computePrice($item, $qte)
    {
        $discounts = [
            'Cherries' => function($item, $qte) {
                $price = $this->productPrices[$item] * $qte;

                $packs = intval($qte/2);

                $price -= 20 * $packs;

                return $price;
            },
        ];

        if(array_key_exists($item, $discounts))
        {
            return $discounts[$item]($item, $qte);
        }

        return $this->productPrices[$item] * $qte;
    }
}
