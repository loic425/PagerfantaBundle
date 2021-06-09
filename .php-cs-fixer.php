<?php declare(strict_types=1);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP71Migration' => true,
        '@PHP71Migration:risky' => true,
        '@PHPUnit84Migration:risky' => true,
        'blank_line_after_opening_tag' => false,
        'fopen_flags' => false,
        'linebreak_after_opening_tag' => false,
        'no_superfluous_phpdoc_tags' => ['remove_inheritdoc' => true],
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        (new PhpCsFixer\Finder())
            ->notPath('vendor')
            ->in(__DIR__)
            ->append([__DIR__.'/BabDevPagerfantaBundle.php', __FILE__])
    )
;
