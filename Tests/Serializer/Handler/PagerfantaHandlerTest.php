<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\Serializer\Handler;

use BabDev\PagerfantaBundle\Serializer\Handler\PagerfantaHandler;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Pagerfanta\Adapter\NullAdapter;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PagerfantaHandlerTest extends TestCase
{
    public function testSerializeToJson(): void
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

        /** @var SerializationContext&MockObject $context */
        $context = $this->createMock(SerializationContext::class);

        /** @var SerializationVisitorInterface&MockObject $visitor */
        $visitor = $this->createMock(SerializationVisitorInterface::class);
        $visitor->expects($this->once())
            ->method('visitArray')
            ->with($this->isType('array'), [])
            ->willReturn($expectedResultArray);

        $this->assertEquals(
            $expectedResultArray,
            (new PagerfantaHandler())->serializeToJson($visitor, $pager, [], $context)
        );
    }
}
