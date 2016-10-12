# eveseat-web-onboarding

This [EVE SeAT](https://github.com/eveseat) package adds custom onboarding for new users.

- Displays registration next steps listing on home screen
- Registration email confirmation and account activation
- Override API key additions to allow for grandfathered legacy keys that don't meet the minimum mask requirements

## Install

#### Add Package

Run the following command while in your SeAT install:

```bash
$ composer require eve-scout/eveseat-web-onboarding
```

#### Update Configs

* Open `config/app.php` in your favorite editor.
* Add `EveScout\Seat\Web\Onboarding\OnboardingServiceProvider::class` to the bottom of the `providers` array.

## Credits

  - [Johnny Splunk](http://github.com/johnnysplunk)

## License

[GPL v2 License](https://opensource.org/licenses/GPL-2.0)

Copyright (c) 2016 Johnny Splunk of EVE-Scout <[https://twitter.com/eve_scout](https://twitter.com/eve_scout)>