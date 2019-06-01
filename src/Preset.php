<?php

namespace harmonic\LaravelPreset;

use sixlive\DotenvEditor\DotenvEditor;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Console\Presets\Preset as BasePreset;
use Illuminate\Filesystem\Filesystem;

class Preset extends BasePreset {
    protected $command;
    protected $options = [];
    protected static $installTheme = false;
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
    protected static $jsInclude = [
        '@babel/plugin-syntax-dynamic-import' => '^7.2.0',
        'browser-sync' => '^2.26.5',
        'browser-sync-webpack-plugin' => '2.0.1',
    ];
    protected static $jsExclude = [];

    public function __construct($command) {
        $this->command = $command;
    }

    public static function install($command) {
        (new static($command))->run();
    }

    public function run() {
        $this->options = $this->gatherOptions();

        if ($this->options['theme']) {
            static::$installTheme = true;
            $this->command->task('Install harmonic theme', function () {
                return $this->installTheme();
            });
        }

        if ($this->options['install_inertia']) {
            $this->packages['inertiajs/inertia-laravel'] = [
                'repo' => 'https://github.com/inertiajs/inertia-laravel',
                'version' => 'dev-master'
            ];
            $this->jsInclude = array_merge($this->jsInclude, [
                'inertia' => 'github:inertiajs/inertia',
                'inertia-vue' => 'inertiajs/inertia-vue',
                'vue-template-compiler' => '^2.6.10',
            ]);
        }

        if (!empty($this->options['packages'])) {
            $this->command->task('Install composer dependencies', function () {
                return $this->updateComposerPackages();
            });
        }
        $this->command->task('Install composer dev-dependencies', function () {
            return $this->updateComposerDevPackages();
        });

        $this->command->task('Update ENV files', function () {
            $this->updateEnvFile();
        });

        if ($this->options['install_tailwind']) {
            $this->jsInclude = array_merge($this->jsInclude, [
                'laravel-mix-purgecss' => '^4.1.0',
                'postcss-import' => '^12.0.1',
                'postcss-nesting' => '^7.0.0',
                'tailwindcss' => '>=1.0.0'
            ]);

            $this->jsExclude = array_merge($this->jsExclude, [
                'bootstrap',
                'bootstrap-sass',
                'jquery',
            ]);

            $this->command->task('Install Tailwindcss', function () {
                static::ensureComponentDirectoryExists();
                static::updatePackages();
                $this->tailwindTemplate();
                static::removeNodeModules();
            });
            $this->command->task('Install node dependencies with Yarn', function () {
                $this->runCommand('yarn install');
            });
            $this->command->task('Setup Tailwindcss', function () {
                $this->runCommand('yarn tailwind init');
            });
            $this->command->task('Run node dev build with Yarn', function () {
                $this->runCommand('yarn dev');
            });
        }

        $this->updateGitignore();

        if ($this->options['remove_after_install']) {
            $this->command->task('Remove harmonic/laravel-preset', function () {
                $this->runCommand('composer remove harmonic/laravel-preset');
                $this->runCommand('composer dumpautoload');
            });
        }

        $this->outputSuccessMessage();
    }

    private function tailwindTemplate() {
        tap(new Filesystem, function ($files) {
            $files->deleteDirectory(resource_path('sass'));
            $files->delete(public_path('css/app.css'));
            if (!$files->isDirectory($directory = resource_path('css'))) {
                $files->makeDirectory($directory, 0755, true);
            }
        });
        if (!$this->options['theme']) { // theme has its own settings
            copy(__DIR__ . '/stubs/tailwind/resources/css/app.css', resource_path('css/app.css'));
            copy(__DIR__ . '/stubs/tailwind/webpack.mix.js', base_path('webpack.mix.js'));
            tap(new Filesystem, function ($files) {
                $files->delete(resource_path('views/home.blade.php'));
                $files->delete(resource_path('views/welcome.blade.php'));
                $files->copyDirectory(__DIR__ . '/stubs/tailwind/resources/views', resource_path('views'));
            });
        }
    }

    protected static function updatePackageArray(array $packages) {
        return array_merge(static::$jsInclude, Arr::except($packages, static::$jsExclude));
    }

    private function gatherOptions() {
        $options = [
            'settings' => $this->promptForSettings(),
            'theme' => $this->command->confirm('Install harmonic theme?', true),
            'packages' => $this->promptForPackagesToInstall(),
            'remove_after_install' => $this->command->confirm('Remove harmonic/laravel-preset after install?', true),
        ];

        if (!$options['theme']) {
            $options['install_tailwind'] = $this->command->confirm('Install Tailwindcss?', true);
            $options['install_inertia'] = $this->command->confirm('Install Inertia JS?', true);
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

    private function promptForSettings() {
        $name = $this->command->ask('Project name (long for use in .env): ');
        $uri = $this->command->ask('Short name (will be used to create url project-name.test): ');
        $dbName = $this->command->ask('DB name (MUST EXIST): ', $uri);

        return [
            'name' => $name,
            'uri' => $uri,
            'db' => $dbName
        ];
    }

    private function updateComposerPackages() {
        $this->runCommand(sprintf(
            'composer require %s',
            $this->resolveForComposer($this->options['packages'])
        ));
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

    private function resolveForComposer($packages) {
        return Collection::make($packages)
            ->transform(function ($package) {
                return isset($this->packages[$package]['version'])
                    ? $package . ':' . $this->packages[$package]['version']
                    : $package;
            })
            ->implode(' ');
    }

    private function updateComposerDevPackages() {
        $this->runCommand(sprintf(
            'composer require --dev %s',
            $this->resolveForComposer($this->devPackages())
        ));
    }

    private function installTheme() {
        $this->options['install_tailwind'] = true;
        $this->options['install_inertia'] = true;

        $this->jsInclude = array_merge($this->jsInclude, [
            'portal-vue' => '^2.1.4',
        ]);

        $themePackages = Collection::make($this->themePackages)->keys()->toArray();

        // Add all the composer theme related packages to the list to install
        $this->options['packages'] = array_merge($this->options['packages'], $themePackages);
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

        return true;
    }

    private function updateEnvFile() {
        tap(new DotenvEditor, function ($editor) {
            $editor->load(base_path('.env'));
            $editor->set('DB_DATABASE', $this->options['settings']['db']);
            $editor->set('DB_USERNAME', 'root');
            $editor->set('DB_PASSWORD', 'root');
            $editor->set('APP_NAME', '"' . $this->options['settings']['name'] . '"');
            $editor->set('APP_URL', 'http://' . $this->options['settings']['uri'] . 'test');
            $editor->save();
        });
        tap(new DotenvEditor, function ($editor) {
            $editor = new DotenvEditor;
            $editor->load(base_path('.env.example'));
            $editor->set('DB_DATABASE', $this->options['settings']['db']);
            $editor->set('DB_USERNAME', 'root');
            $editor->set('DB_PASSWORD', 'root');
            $editor->set('APP_NAME', '"' . $this->options['settings']['name'] . '"');
            $editor->set('APP_URL', 'http://' . $this->options['settings']['uri'] . 'test');
            $editor->save();
        });
    }

    private function updateGitignore() {
        copy(__DIR__ . '/stubs/gitignore-stub', base_path('.gitignore'));
    }

    private function runCommand($command) {
        return exec(sprintf('%s 2>&1', $command));
    }

    private function getInstalledPackages() {
        return Collection::make($this->packages)
            ->filter(function ($data, $package) {
                return in_array($package, $this->options['packages'])
                    || ($data['dev'] ?? false);
            })
            ->toArray();
    }

    private function outputSuccessMessage() {
        $this->command->line('');
        $this->command->info('Preset installation complete. The packages that were installed may require additional installation steps.');
        $this->command->line('');
        foreach ($this->getInstalledPackages() as $package => $packageData) {
            $this->command->getOutput()->writeln(vsprintf('- %s: <comment>%s</comment>', [
                $package,
                $packageData['repo'],
            ]));
        }
    }
}
