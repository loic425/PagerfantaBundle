<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\Serializer\Normalizer;

use BabDev\PagerfantaBundle\Serializer\Normalizer\PagerfantaNormalizer;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Adapter\NullAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Serializer;

final class PagerfantaNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $pager = new Pagerfanta(
            new NullAdapter(25)
        );

        $expectedResultArray = [
            'items' => $pager->getCurrentPageResults(),
            'pagination' => [
                'current_page' => $pager->getCurrentPage(),
                'has_previous_page' => $pager->hasPreviousPage(),
                'has_next_page' => $pager->hasNextPage(),
                'per_page' => $pager->getMaxPerPage(),
                'total_items' => $pager->getNbResults(),
                'total_pages' => $pager->getNbPages(),
            ],
        ];

        $this->assertEquals(
            $expectedResultArray,
            (new PagerfantaNormalizer())->normalize($pager)
        );
    }

    public function testNormalizeOnlyAcceptsPagerfantaInstances(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('The object must be an instance of "%s".', PagerfantaInterface::class));

        (new PagerfantaNormalizer())->normalize(new \stdClass());
    }

    public function dataSupportsNormalization(): \Generator
    {
        yield 'Supported' => [new Pagerfanta(new NullAdapter(25)), true];
        yield 'Not Supported' => [new \stdClass(), false];
    }

    /**
     * @param mixed $data
     *
     * @dataProvider dataSupportsNormalization
     */
    public function testSupportsNormalization($data, bool $supported): void
    {
        $this->assertSame($supported, (new PagerfantaNormalizer())->supportsNormalization($data));
    }

    public function testHasCacheableSupportsMethod(): void
    {
        $this->assertTrue((new PagerfantaNormalizer())->hasCacheableSupportsMethod());
    }

    public function testIteSerializesIterableData(): void
    {
        $serializer = new Serializer([new PagerfantaNormalizer()], [new JsonEncoder()]);
        $generator = static function (): iterable {
            yield '1';
            yield '2';
            yield '3';
            yield '4';
            yield '5';
        };
        $pager = new Pagerfanta(new FixedAdapter(5, $generator()));

        $expectedResultArray = [
            'items' => iterator_to_array($pager->getCurrentPageResults()),
            'pagination' => [
                'current_page' => $pager->getCurrentPage(),
                'has_previous_page' => $pager->hasPreviousPage(),
                'has_next_page' => $pager->hasNextPage(),
                'per_page' => $pager->getMaxPerPage(),
                'total_items' => $pager->getNbResults(),
                'total_pages' => $pager->getNbPages(),
            ],
        ];

        self::assertSame($expectedResultArray, \json_decode($serializer->serialize($pager, 'json'), true));
    }
}
