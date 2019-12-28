<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\DependencyInjection;

use BabDev\PagerfantaBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testDefaultConfig(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), []);

        $this->assertEquals(self::getBundleDefaultConfig(), $config);
    }

    public function testConfigWithCustomDefaultView(): void
    {
        $extraConfig = [
            'default_view' => 'custom_view',
        ];

        $config = (new Processor())->processConfiguration(new Configuration(), [$extraConfig]);

        $this->assertEquals(
            array_merge(self::getBundleDefaultConfig(), $extraConfig),
            $config
        );
    }

    public function testConfigWithCustomExceptionsStrategy(): void
    {
        $extraConfig = [
            'exceptions_strategy' => [
                'out_of_range_page' => 'custom_handler',
                'not_valid_current_page' => 'custom_handler',
            ],
        ];

        $config = (new Processor())->processConfiguration(new Configuration(), [$extraConfig]);

        $this->assertEquals(
            array_merge(self::getBundleDefaultConfig(), $extraConfig),
            $config
        );
    }

    protected static function getBundleDefaultConfig(): array
    {
        return [
            'default_view' => 'default',
            'exceptions_strategy' => [
                'out_of_range_page' => Configuration::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND,
                'not_valid_current_page' => Configuration::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND,
            ],
        ];
    }
}
