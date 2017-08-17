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
        $productPrices,
        $isCsvAllowed;

    public function __construct()
    {
        parent::__construct();

        $this->isCsvAllowed = true;

        $this->productPrices = [
            'Apples' => 100,
            'Mele' => 100,
            'Pommes' => 100,
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
        $list = [];

        while (true)
        {
            $products = $this->getProducts($input, $output);

            foreach($products as $product)
            {
                $product = trim($product);
                //$product = $this->translateProduct($product);

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

        return $this->applyGlobalDiscount($total, $list);
    }

    private function computePrice($item, $qte)
    {
        $discounts = [
            'Pommes' => function($item, $qte) {
                $price = intval($qte/3) * 200;
                $price += $this->productPrices[$item] * ($qte%3);

                return $price;
            },
            'Mele' => function($item, $qte) {
                $price = intval($qte/2) * 100;
                $price += $this->productPrices[$item] * ($qte%2);

                return $price;
            },
            'Cherries' => function($item, $qte) {
                $price = $this->productPrices[$item] * $qte;

                $packs = intval($qte/2);

                $price -= 20 * $packs;

                return $price;
            },
            'Bananas' => function($item, $qte) {
                return $this->productPrices[$item] * ceil($qte/2);
            },
        ];

        $price = $this->productPrices[$item] * $qte;
        if(array_key_exists($item, $discounts))
        {
            $price = $discounts[$item]($item, $qte);
        }

        return $price;
    }

    private function applyGlobalDiscount($price, $items)
    {
        $countTotal = 0;
        $countApple = 0;
        foreach($items as $product => $qte)
        {
            $countTotal += $qte;
            if ($this->translateProduct($product) === 'Apples') {
                $countApple += $qte;
            }
        }
        $price -= intval($countTotal/5) * 200;
        $price -= intval($countApple/4) * 100;

        return $price;
    }

    private function getProducts(InputInterface $input, OutputInterface $output)
    {
        $helper = new QuestionHelper();
        $question = new Question(sprintf('Please enter the product name (%s): ', implode(' , ', array_keys($this->productPrices))), '');

        if($this->isCsvAllowed === true)
        {
            return explode(',', $helper->ask($input, $output, $question));
        }

        return [$helper->ask($input, $output, $question)];
    }

    private function translateProduct($productName)
    {
        $translateList = [
            'Apples' => ['Pommes', 'Mele'],
        ];

        foreach($translateList as $translation => $words)
        {
            if(in_array($productName, $words))
            {
                return $translation;
            }
        }

        return $productName;
    }
}
