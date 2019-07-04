# Harmonic Laravel Preset

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

A Laravel preset that can create a basic Laravel install with some additional composer packages and an optional starting admin theme using InertiaJS and Tailwind CSS as a quick start.

## Installation

Assumes a new Laravel installation. Then install the preset with composer.

``` bash
$ composer require harmonic/laravel-preset --dev
```

## Usage

``` bash
$ php artisan preset harmonic
```

## What's included?

### Composer Packages

- [harmonic/laravel-envcoder](https://github.com/Harmonic/laravel-envcoder) (dev)
- [bensampo/laravel-enum](https://github.com/BenSampo/laravel-enum) - optional
- [silber/bouncer:v1.0.0-rc.4](https://github.com/JosephSilber/bouncer) - optional
- [dyrynda/laravel-make-user](https://github.com/michaeldyrynda/laravel-make-user) (required if theme used)
- [sempro/phpunit-pretty-print](https://github.com/sempro/phpunit-pretty-print) (dev)
- [sensiolabs/security-checker](https://github.com/sensiolabs/security-checker) (dev)
- [harmonic/inertia-table](https://github.com/Harmonic/inertia-table) (required if theme used)

### Frontend

- [VueJS](https://github.com/vuejs/vue)
- [InertiaJS](https://github.com/inertiajs/inertia)
- [Tailwind CSS](https://github.com/tailwindcss/tailwindcss)
- [Cypress](https://github.com/cypress-io/cypress)
- [Inertia Table Vue](https://github.com/Harmonic/inertia-table-vue)
- [Vue Tailwind Modal](https://github.com/Harmonic/vue-tailwind-modal)

### Stubs

- .gitignore (to include compiled assets)
- Removes sass, bootstrap and jquery
- Inertia JS configuration (installed with theme)
- Tailwind config (installed with theme)
- Cypress config (placing cypress inside /tests folder)
- Authentication stubs (installed with theme)
- Tailwind admin theme based on Ping CRM interface

## Customisation

Once installed you are free to modify all /resources as you see fit. You can easily customise the colour scheme of the theme by editing tailwind.config.js in the project root with the [Tailwind colours](https://tailwindcss.com/docs/customizing-colors/#default-color-palette) of your choice:

``` js
module.exports = {
	theme: {
		extend: {
			colors: {
				primary: colors.indigo,
				secondary: colors.orange,
				// ...
			  }
		}
	}
  }
```

## Credits

- [Craig Harman][link-author]
- [All Contributors][link-contributors]
- Based on [sixlive laravel preset](https://github.com/sixlive/laravel-preset)
- Admin UI based on [Ping CRM](https://github.com/inertiajs/pingcrm)

## License

Copyright Harmonic New Media

[ico-version]: https://img.shields.io/packagist/v/harmonic/laravel-preset.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/harmonic/laravel-preset.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/harmonic/laravel-preset
[link-downloads]: https://packagist.org/packages/harmonic/laravel-preset
[link-author]: https://github.com/harmonic
[link-contributors]: ../../contributors