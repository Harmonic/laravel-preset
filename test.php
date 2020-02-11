<?php

/**
 * Script to setup a fresh laravel install for testing out the preset
 */


use Symfony\Component\Process\Process;

require_once 'vendor/autoload.php';

function run(string $command)
{
    echo 'COMMAND > ' . $command . "\n";
    $process = Process::fromShellCommandline($command);
    $process->run(function ($type, $buffer) {
        if (Process::ERR === $type) {
            echo 'ERR > ' . $buffer;
        } else {
            echo 'OUT > ' . $buffer;
        }
    });
}

$pathToInstall = '/tmp/testing-preset';

run("rm -rf $pathToInstall");
run('cd /tmp && composer create-project --prefer-dist laravel/laravel testing-preset');

$composerJson = json_decode(file_get_contents($pathToInstall . '/composer.json'), true);
echo json_last_error_msg();
$composerJson['repositories'][] = ['type' => 'path', 'url' => __DIR__];
$composerJson['require']['harmonic/laravel-preset'] = '*';

file_put_contents($pathToInstall . '/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));

run("cd $pathToInstall && composer update");

