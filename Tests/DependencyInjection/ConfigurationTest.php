<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\DependencyInjection;

use BabDev\PagerfantaBundle\DependencyInjection\Configuration;
use BabDev\PagerfantaBundle\View\TwigView;
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

    public function testConfigWithCustomDefaultTwigTemplate(): void
    {
        $extraConfig = [
            'default_twig_template' => 'custom.html.twig',
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
                'out_of_range_page' => Configuration::EXCEPTION_STRATEGY_CUSTOM,
                'not_valid_current_page' => Configuration::EXCEPTION_STRATEGY_CUSTOM,
            ],
        ];

        $config = (new Processor())->processConfiguration(new Configuration(), [$extraConfig]);

        $this->assertEquals(
            array_merge(self::getBundleDefaultConfig(), $extraConfig),
            $config
        );
    }

    public function testConfigWithDeprecatedExceptionsStrategy(): void
    {
        $extraConfig = [
            'exceptions_strategy' => [
                'out_of_range_page' => 'deprecated',
                'not_valid_current_page' => 'deprecated',
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
            'default_twig_template' => TwigView::DEFAULT_TEMPLATE,
            'exceptions_strategy' => [
                'out_of_range_page' => Configuration::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND,
                'not_valid_current_page' => Configuration::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND,
            ],
        ];
    }
}
