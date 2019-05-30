<?php

namespace harmonic\LaravelPreset;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\PresetCommand;

class PresetServiceProvider extends ServiceProvider {
    public function boot() {
        PresetCommand::macro('harmonic', function ($command) {
            // Do the preset work
            Preset::install($command);

            // Let the user know what we've done
            $command->info('The harmonic preset has been installed successfully.');
        });
    }
}
