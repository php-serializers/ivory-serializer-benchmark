<?php

namespace Ivory\Tests\Serializer\Benchmark;

use Ivory\Tests\Serializer\Benchmark\Model\Category;
use Ivory\Tests\Serializer\Benchmark\Model\Comment;
use Ivory\Tests\Serializer\Benchmark\Model\Forum;
use Ivory\Tests\Serializer\Benchmark\Model\Thread;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class SymfonyCustomNormalizerBenchmark extends AbstractBenchmark
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->serializer = new Serializer([
            new DateTimeNormalizer(),
            new class implements NormalizerInterface, CacheableSupportsMethodInterface
            {
                public function hasCacheableSupportsMethod(): bool
                {
                    return true;
                }

                public function normalize($object, $format = null, array $context = array())
                {
                    assert($object instanceof Forum);

                    return [
                        'id' => $object->getId(),
                        'name' => $object->getName(),
                        'threads' => $object->getThreads(),
                        'category' => $object->getCategory(),
                        'createdAt' => $object->getCreatedAt(),
                        'updatedAt' => $object->getUpdatedAt(),
                    ];
                }

                public function supportsNormalization($data, $format = null)
                {
                    return $data instanceof Forum;
                }
            },
            new class implements NormalizerInterface, CacheableSupportsMethodInterface
            {
                public function hasCacheableSupportsMethod(): bool
                {
                    return true;
                }

                public function normalize($object, $format = null, array $context = array())
                {
                    assert($object instanceof Thread);

                    return [
                        'id' => $object->getId(),
                        'popularity' => $object->getPopularity(),
                        'title' => $object->getTitle(),
                        'comments' => $object->getComments(),
                        'description' => $object->getDescription(),
                        'createdAt' => $object->getCreatedAt(),
                        'updatedAt' => $object->getUpdatedAt(),
                    ];
                }

                public function supportsNormalization($data, $format = null)
                {
                    return $data instanceof Thread;
                }
            },
            new class implements NormalizerInterface, CacheableSupportsMethodInterface
            {
                public function hasCacheableSupportsMethod(): bool
                {
                    return true;
                }

                public function normalize($object, $format = null, array $context = array())
                {
                    assert($object instanceof Comment);

                    return [
                        'id' => $object->getId(),
                        'content' => $object->getContent(),
                        'author' => $object->getAuthor(),
                        'createdAt' => $object->getCreatedAt(),
                        'updatedAt' => $object->getUpdatedAt(),
                    ];
                }

                public function supportsNormalization($data, $format = null)
                {
                    return $data instanceof Comment;
                }
            },
            new class implements NormalizerInterface, CacheableSupportsMethodInterface
            {
                public function hasCacheableSupportsMethod(): bool
                {
                    return true;
                }

                public function normalize($object, $format = null, array $context = array())
                {
                    assert($object instanceof Category);

                    return [
                        'id' => $object->getId(),
                        'parent' => $object->getParent(),
                        'children' => $object->getChildren(),
                        'createdAt' => $object->getCreatedAt(),
                        'updatedAt' => $object->getUpdatedAt(),
                    ];
                }

                public function supportsNormalization($data, $format = null)
                {
                    return $data instanceof Category;
                }
            },
        ], [
            new JsonEncoder(), new XmlEncoder(), new YamlEncoder()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function execute($horizontalComplexity = 1, $verticalComplexity = 1)
    {
        return $this->serializer->serialize(
            $this->getData($horizontalComplexity, $verticalComplexity),
            $this->getFormat()
        );
    }
}
