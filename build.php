#!/usr/bin/env php
<?php

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Helper Functions
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function ask(string $question, string $default = ''): string
{
    $answer = readline($question.($default ? " ({$default})" : null).': ');

    if (! $answer) {
        return $default;
    }

    return $answer;
}

function confirm(string $question, bool $default = false): bool
{
    $answer = ask($question.' ('.($default ? 'Y/n' : 'y/N').')');

    if (! $answer) {
        return $default;
    }

    return strtolower($answer) === 'y';
}

function writeln(string $line): void
{
    echo $line.PHP_EOL;
}

function section(string $title): void
{
    writeln('');
    writeln(' ── '.$title.' ──');
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Replacement Helpers
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function replaceInFile(string $file, string $search, string $replace): bool
{
    if (! file_exists($file)) {
        return false;
    }

    $content = file_get_contents($file);
    $newContent = str_replace($search, $replace, $content);

    if ($newContent === $content) {
        return false;
    }

    file_put_contents($file, $newContent);

    return true;
}

function replaceInFiles(array $files, string $search, string $replace): array
{
    $changed = [];

    foreach ($files as $file) {
        if (replaceInFile($file, $search, $replace)) {
            $changed[] = $file;
        }
    }

    return $changed;
}

function findPhpFiles(string $dir): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    /** @var SplFileInfo $file */
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $files[] = $file->getRealPath();
        }
    }

    sort($files);

    return $files;
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Step 1: Collect Input
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

writeln('╔══════════════════════════════════════════╗');
writeln('║     Laravel Package Initialization       ║');
writeln('╚══════════════════════════════════════════╝');

section('Package Information');

$packageName = ask('Package name (kebab-case, e.g. "my-awesome-package")', 'package');
$packageDescription = ask('Package description (one-line)', 'A Laravel package');
$authorName = ask('Author name', 'Your Name');

section('Vendor / Organization');

$vendorOrg = ask('GitHub org/username', 'xbot-team');

section('PHP Namespace');

$namespaceVendor = ask('Namespace vendor part (e.g. "XBot")', 'XBot');
$namespacePackage = ask('Namespace package part (e.g. "Package")', 'Package');

$fullNamespace = $namespaceVendor.'\\'.$namespacePackage;

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Step 2: Confirmation Summary
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

section('Confirmation');

writeln('  Package name       : '.$packageName);
writeln('  Description        : '.$packageDescription);
writeln('  Author             : '.$authorName);
writeln('  GitHub org/user    : '.$vendorOrg);
writeln('  PHP namespace      : '.$fullNamespace);
writeln('');

if (! confirm('Proceed with these values?', true)) {
    writeln('Aborted.');
    exit(0);
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Step 3: Apply Replacements
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

writeln('');
writeln('Applying replacements...');

$allPhpFiles = findPhpFiles(__DIR__.'/src');

$textFiles = [
    __DIR__.'/composer.json',
    __DIR__.'/README.md',
    __DIR__.'/CHANGELOG.md',
    __DIR__.'/LICENSE.md',
    __DIR__.'/AGENTS.md',
];

$changed = [];

// ── 3a. Replace :package_name ──

$changed = array_merge(
    $changed,
    replaceInFiles($textFiles, ':package_name', $packageName)
);
$changed = array_merge(
    $changed,
    replaceInFiles($allPhpFiles, ':package_name', $packageName)
);

// ── 3b. Replace :package_description ──

$changed = array_merge(
    $changed,
    replaceInFiles($textFiles, ':package_description', $packageDescription)
);

// ── 3c. Replace :author_name ──

$changed = array_merge(
    $changed,
    replaceInFiles($textFiles, ':author_name', $authorName)
);

// ── 3d. Replace vendor/org (xbot-team → new vendor) ──

$vendorFiles = [
    __DIR__.'/composer.json',
    __DIR__.'/README.md',
    __DIR__.'/LICENSE.md',
];
$changed = array_merge(
    $changed,
    replaceInFiles($vendorFiles, 'xbot-team', $vendorOrg)
);

// ── 3e. Replace PHP namespace in src files ──

$oldNamespace = 'XBot\\Package';
$newNamespace = $namespaceVendor.'\\'.$namespacePackage;

if ($oldNamespace !== $newNamespace) {
    // Namespace declaration
    $changed = array_merge(
        $changed,
        replaceInFiles($allPhpFiles, 'namespace '.$oldNamespace, 'namespace '.$newNamespace)
    );

    // Use statements (references to the old namespace)
    $changed = array_merge(
        $changed,
        replaceInFiles($allPhpFiles, 'use '.$oldNamespace, 'use '.$newNamespace)
    );
}

// ── 3f. Update composer.json autoload + extra ──

$composerJson = __DIR__.'/composer.json';
if (file_exists($composerJson)) {
    $composerContent = file_get_contents($composerJson);

    // Namespace as it appears in JSON (double backslashes)
    $oldNsJson = str_replace('\\', '\\\\', $oldNamespace); // XBot\\Package
    $newNsJson = str_replace('\\', '\\\\', $newNamespace); // MyOrg\\AwesomePkg

    // Update PSR-4 autoload key: "XBot\\Package\\": "src/"
    $composerContent = str_replace(
        '"'.$oldNsJson.'\\\\":',
        '"'.$newNsJson.'\\\\":',
        $composerContent
    );

    // Update autoload-dev key: "XBot\\Package\\Tests\\": "tests/"
    $composerContent = str_replace(
        '"'.$oldNsJson.'\\\\Tests\\\\":',
        '"'.$newNsJson.'\\\\Tests\\\\":',
        $composerContent
    );

    // Update extra.providers: "XBot\\Package\\PackageServiceProvider"
    $composerContent = str_replace(
        '"'.$oldNsJson.'\\\\PackageServiceProvider"',
        '"'.$newNsJson.'\\\\PackageServiceProvider"',
        $composerContent
    );

    // Update extra.aliases: "XBot\\Package\\Facades\\Package"
    $composerContent = str_replace(
        '"'.$oldNsJson.'\\\\Facades\\\\Package"',
        '"'.$newNsJson.'\\\\Facades\\\\Package"',
        $composerContent
    );

    file_put_contents($composerJson, $composerContent);
    $changed[] = $composerJson;
}

// ── 3g. Update README.md code samples ──

$readmeFile = __DIR__.'/README.md';
if (file_exists($readmeFile) && $oldNamespace !== $newNamespace) {
    $readmeContent = file_get_contents($readmeFile);

    // Replace XBot\Package\ → newNamespace\ in longer class references
    $readmeContent = str_replace(
        $oldNamespace.'\\',
        $newNamespace.'\\',
        $readmeContent
    );

    // Also replace standalone XBot\Package (e.g. "new XBot\Package()")
    $readmeContent = str_replace(
        $oldNamespace,
        $newNamespace,
        $readmeContent
    );

    file_put_contents($readmeFile, $readmeContent);
    $changed[] = $readmeFile;
}

// ── 3h. Update ServiceProvider config key + publish tags ──

$serviceProviderFile = __DIR__.'/src/PackageServiceProvider.php';
$packageLower = strtolower($packageName);

if (file_exists($serviceProviderFile) && $packageName !== 'package') {
    $spContent = file_get_contents($serviceProviderFile);

    // --- CONFIG_KEY ---
    // "CONFIG_KEY = 'package'" → "CONFIG_KEY = '{$packageLower}'"
    $spContent = preg_replace(
        "/private const CONFIG_KEY = '[^']+';/",
        "private const CONFIG_KEY = '{$packageLower}';",
        $spContent
    );

    // --- Publish tags ---
    $spContent = preg_replace(
        "/'package-config'/",
        "'{$packageLower}-config'",
        $spContent
    );
    $spContent = preg_replace(
        "/'package-migrations'/",
        "'{$packageLower}-migrations'",
        $spContent
    );
    $spContent = preg_replace(
        "/'package-views'/",
        "'{$packageLower}-views'",
        $spContent
    );
    $spContent = preg_replace(
        "/'package-lang'/",
        "'{$packageLower}-lang'",
        $spContent
    );

    // --- View/lang namespace string (second arg of loadViewsFrom/loadTranslationsFrom) ---
    // "loadViewsFrom(..., 'package')" → "loadViewsFrom(..., '{$packageLower}')"
    $spContent = preg_replace(
        "/(loadViewsFrom\([^,]+,\s*)'package'/",
        "\$1'{$packageLower}'",
        $spContent
    );
    $spContent = preg_replace(
        "/(loadTranslationsFrom\([^,]+,\s*)'package'/",
        "\$1'{$packageLower}'",
        $spContent
    );

    // --- Publish target paths (RHS only, not source paths on disk) ---
    $spContent = str_replace(
        "resource_path('views/vendor/package')",
        "resource_path('views/vendor/{$packageLower}')",
        $spContent
    );
    $spContent = str_replace(
        "lang_path('vendor/package')",
        "lang_path('vendor/{$packageLower}')",
        $spContent
    );

    file_put_contents($serviceProviderFile, $spContent);
    $changed[] = $serviceProviderFile;
}

// ── 3i. Update config/package.php env prefix ──

$configFile = __DIR__.'/config/package.php';
if (file_exists($configFile) && $packageName !== 'package') {
    $configContent = file_get_contents($configFile);

    $oldPrefix = 'PACKAGE_';
    $newPrefix = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '_', $packageName)).'_';
    $configContent = str_replace($oldPrefix, $newPrefix, $configContent);

    file_put_contents($configFile, $configContent);
    $changed[] = $configFile;
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Summary
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

$changed = array_unique($changed);

section('Results');
writeln('Modified '.count($changed).' file(s):');

foreach ($changed as $file) {
    writeln('  • '.str_replace(__DIR__.'/', '', $file));
}

writeln('');
writeln('✓ Package initialization complete!');

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Self-destruct
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

writeln('');
writeln('Next steps:');
writeln('  1. Run: composer install');
writeln('  2. Review the modified files');
writeln('  3. Initialize git: git init && git add . && git commit -m "Initial commit"');
writeln('  4. Push to your repository');

if (confirm('Do you want this file to self-destruct?', true)) {
    unlink(__FILE__);
    writeln('build.php removed.');
}
