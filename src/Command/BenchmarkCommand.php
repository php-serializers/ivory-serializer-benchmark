<?php

namespace Ivory\Tests\Serializer\Benchmark\Command;

use Ivory\Tests\Serializer\Benchmark\IvoryBenchmark;
use Ivory\Tests\Serializer\Benchmark\JmsBenchmark;
use Ivory\Tests\Serializer\Benchmark\JmsMinimalBenchmark;
use Ivory\Tests\Serializer\Benchmark\JsonSerializableBenchmark;
use Ivory\Tests\Serializer\Benchmark\Result\BenchmarkResultInterface;
use Ivory\Tests\Serializer\Benchmark\Runner\BenchmarkRunner;
use Ivory\Tests\Serializer\Benchmark\SerializardClosureBenchmark;
use Ivory\Tests\Serializer\Benchmark\SerializardReflectionBenchmark;
use Ivory\Tests\Serializer\Benchmark\SymfonyCustomNormalizerBenchmark;
use Ivory\Tests\Serializer\Benchmark\SymfonyGetSetNormalizerBenchmark;
use Ivory\Tests\Serializer\Benchmark\SymfonyObjectNormalizerBenchmark;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BenchmarkCommand extends Command
{
    /**
     * @var BenchmarkRunner
     */
    private $runner;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->runner = new BenchmarkRunner();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('benchmark')
            ->addOption('iteration', 'i', InputArgument::OPTIONAL, 'Number of iteration(s)', 1)
            ->addOption('horizontal-complexity', 'hc', InputArgument::OPTIONAL, 'Horizontal data complexity', 1)
            ->addOption('vertical-complexity', 'vc', InputArgument::OPTIONAL, 'Vertical data complexity', 1)
            ->addOption('serializer', 's', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'the serializer(s) to use', null);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($serializers = $input->getOption('serializer')) {
            $benchmarks = [];
            foreach ($serializers as $serializer) {
                if (!class_exists($serializer)) {
                    $serializer = "\Ivory\Tests\Serializer\Benchmark\\{$serializer}Benchmark";
                    if (!class_exists($serializer)) {
                        throw new \InvalidArgumentException("The class \"{$serializer}\" doesn't exist");
                    }
                }

                $benchmarks[] = new $serializer();
            }
        } else {
            $benchmarks = [
                new IvoryBenchmark(),
                new SymfonyCustomNormalizerBenchmark(),
                new SymfonyObjectNormalizerBenchmark(),
                new SymfonyGetSetNormalizerBenchmark(),
                new JmsBenchmark(),
                new JmsMinimalBenchmark(),
                new JsonSerializableBenchmark(),
                new SerializardClosureBenchmark(),
                new SerializardReflectionBenchmark(),
            ];
        }

        $iteration = $input->getOption('iteration');
        $horizontalComplexity = $input->getOption('horizontal-complexity');
        $verticalComplexity = $input->getOption('vertical-complexity');

        $results = [];
        $longestNameLength = 0;
        $bestResult = PHP_INT_MAX;
        foreach ($benchmarks as $benchmark) {
            $result = $this->runner->run($benchmark, $iteration, $horizontalComplexity, $verticalComplexity);

            $nameLength = strlen(\get_class($benchmark));
            if($nameLength > $longestNameLength) {
                $longestNameLength = $nameLength;
            }
            if($result->getTime() < $bestResult) {
                $bestResult = $result->getTime();
            }

            $results[] = $result;
        }

        usort($results, function(BenchmarkResultInterface $lhs, BenchmarkResultInterface $rhs) {
            return $lhs->getTime() >= $rhs->getTime();
        });

        foreach($results as $result) {
            $output->writeln(vsprintf('%-'.$longestNameLength.'s | %5s | %s', [
                get_class($result->getBenchmark()),
                sprintf('%.2f', $result->getTime() / $bestResult),
                $result->getTime(),
            ]));
        }
    }
}
