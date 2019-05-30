<?php

namespace harmonic\LaravelPreset;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Console\Presets\Preset as BasePreset;
use Illuminate\Filesystem\Filesystem;

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
            'dev' => true,
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
    protected $themePackages = [
        'tightenco/ziggy' => [
            'repo' => 'https://github.com/tightenco/ziggy'
        ],
        'reinink/remember-query-strings' => [
            'repo' => 'https://github.com/reinink/remember-query-strings'
        ]
    ];

    public function __construct($command) {
        $this->command = $command;
    }

    public static function install($command) {
        (new static($command))->run();
    }

    protected static function updatePackageArray(array $packages) {
        if ($this->options['theme']) {
            return array_merge([
                '@babel/plugin-syntax-dynamic-import' => '^7.2.0',
                'inertia' => 'github:inertiajs/inertia',
                'inertia-vue' => 'inertiajs/inertia-vue',
                'vue-template-compiler' => '^2.6.10',
                'laravel-mix-purgecss' => '^4.1.0',
                'browser-sync' => '^2.26.5',
                'browser-sync-webpack-plugin' => '2.0.1',
                'portal-vue' => '^2.1.4',
                'postcss-import' => '^12.0.1',
                'postcss-nesting' => '^7.0.0',
            ], Arr::except($packages, [
                'bootstrap',
                'bootstrap-sass',
                'jquery',
            ]));
        }

        return $packages;
    }

    public function run() {
        $this->options = $this->gatherOptions();

        if ($this->options['theme']) {
            $this->options['install_tailwind'] = true;
            $this->installTheme();
        }

        //TODO: Don't forget to do the following if tailwind css is being installed:
        // $files->deleteDirectory(resource_path('sass'));
        // $files->delete(public_path('js/app.js'));
        // $files->delete(public_path('css/app.css'));

        static::updatePackages();
        $this->updateGitignore();
    }

    private function gatherOptions() {
        $options = [
            'theme' => $this->command->confirm('Install harmonic theme?', true),
            'packages' => $this->promptForPackagesToInstall(),
            'remove_after_install' => $this->command->confirm('Remove harmonic/laravel-preset after install?', true),
        ];

        if (!$options['theme']) {
            $options['install_tailwind'] = $this->command->confirm('Install Tailwindcss?', true);
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
            ->keys()
            ->toArray();
    }

    private function devPackages() {
        return Collection::make($this->packages)
            ->where('dev', true)
            ->keys()
            ->toArray();
    }

    private function installTheme() {
        // Add all the composer theme related packages to the list to install
        $this->packages = array_merge($this->packages, $this->themePackages);

        copy(__DIR__ . '/stubs/theme/webpack.mix.js', base_path('webpack.mix.js'));
        //TODO: Potentially replace laravel.test url with one from set up questions
        copy(__DIR__ . '/stubs/theme/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__ . '/stubs/theme/Kernel.php', app_path('Http/Kernel.php'));

        copy(__DIR__ . '/stubs/theme/Model.php', app_path('Model.php'));
        copy(__DIR__ . '/stubs/theme/User.php', app_path('User.php'));
        copy(__DIR__ . '/stubs/theme/web.php', base_path('routes/web.php'));
        copy(__DIR__ . '/stubs/theme/AppServiceProvider.php', app_path('Providers/AppServiceProvider.php'));

        tap(new Filesystem, function ($files) {
            $files->delete(resource_path('views/home.blade.php'));
            $files->delete(resource_path('views/welcome.blade.php'));
            $files->copyDirectory(__DIR__ . '/stubs/theme/views', resource_path('views'));
            $files->copyDirectory(__DIR__ . '/stubs/theme/js', resource_path('js'));
            $files->copyDirectory(__DIR__ . '/stubs/theme/css', resource_path('css'));
            $files->copyDirectory(__DIR__ . '/stubs/theme/Auth', app_path('Http/Controllers/Auth'));
            $files->copyDirectory(__DIR__ . '/stubs/theme/Traits', app_path('Traits'));
        });
    }

    private function updateGitignore() {
        copy(__DIR__ . '/stubs/gitignore-stub', base_path('.gitignore'));
    }
}
