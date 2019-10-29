<?php

declare(strict_types=1);

namespace PhpSerializers\Benchmarks\Bench;

use PhpSerializers\Benchmarks\AbstractBench;
use PhpSerializers\Benchmarks\Model\Forum;
use Symfony\Component\Cache\Simple\ApcuCache;
use Tebru\Gson\Gson;
use Tebru\Gson\PropertyNamingPolicy;

/**
 * @author Nate Brunette <n@tebru.net>
 */
class GsonBenchmark extends AbstractBench
{
    /**
     * @var Gson
     */
    private $gson;

    public function initSerializer(): void
    {
        $cache = new ApcuCache();
        $this->gson = Gson::builder()
            ->enableCache(true)
            ->setCache($cache)
            ->setEnableScalarAdapters(false)
            ->setPropertyNamingPolicy(PropertyNamingPolicy::IDENTITY)
            ->build();
    }

    public function serialize(Forum $data): void
    {
        $this->gson->toJson($data);
    }

    public function getPackageName(): string
    {
        return 'tebru/gson-php';
    }

    public function getNote(): string
    {
        return <<<'NOTE'
Serialize object graphs
NOTE;
    }
}
