<?php

namespace harmonic\LaravelPreset;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Console\Presets\Preset as BasePreset;

class Preset extends BasePreset {
    protected $command;
    protected $options = [];
    protected $packages = [
        'bensampo/laravel-enum' => [
            'repo' => 'https://github.com/BenSampo/laravel-enum',
        ],
        'silber/bouncer' => [
            'repo' => 'https://github.com/JosephSilber/bouncer',
            'version' => 'v1.0.0-rc.4',
        ],
        'harmonic/laravel-envcoder' => [
            'repo' => 'https://github.com/Harmonic/laravel-envcoder',
            'dev' => true,
        ],
        'dyrynda/laravel-make-user' => [
            'repo' => 'https://github.com/michaeldyrynda/laravel-make-user',
            'required' => true, // make it so it won't ask and will just install this
        ],
        'sempro/phpunit-pretty-print' => [
            'repo' => 'https://github.com/Sempro/phpunit-pretty-print',
            'dev' => true,
        ],
        'sensiolabs/security-checker' => [
            'repo' => 'https://github.com/sensiolabs/security-checker',
            'dev' => true,
        ],
    ];

    public function __construct($command) {
        $this->command = $command;
    }

    public static function install($command) {
        (new static($command))->run();
    }

    public function run() {
        $this->options = $this->gatherOptions();
    }

    private function gatherOptions() {
        $options = [
            'theme' => $this->command->confirm('Install harmonic theme?', true),
            'packages' => $this->promptForPackagesToInstall(),

            'remove_after_install' => $this->command->confirm('Remove harmonic/laravel-preset after install?', true),
        ];

        if (!$options['theme']) {
            $options['install_tailwind'] = $this->command->confirm('Install Tailwindcss?', true);
        } else {
            $options['install_tailwind'] = true; //TODO: May be neater to do this in the theme creation process
        }

        return $options;
    }

    private function promptForPackagesToInstall() {
        $possiblePackages = $this->packages();
        $choices = $this->command->choice(
            'Which optional packages should be installed? (e.x. 1,2)',
            array_merge(['all'], $possiblePackages, ['none']),
            '0',
            null,
            true
        );
        if (in_array('all', $choices)) {
            return $possiblePackages;
        }
        if (in_array('none', $choices)) {
            return [];
        }
        return $choices;
    }

    private function packages() {
        return Collection::make($this->packages)
            ->where('dev', false)
            ->where('required', false)
            ->keys()
            ->toArray();
    }
}
