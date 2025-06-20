# Ragnarok Entur sink

Makes Entur data available, so far only NeTEx route data.

## Install

1. Add required repositories to your composer.json:
    ```json
        "repositories": [
            {
                "type": "path",
                "url": "../christmas-tree-parser"
            },
            {
                "type": "path",
                "url": "../laravel-netex"
            },
            {
                "type": "github",
                "url": "https://github.com/example/ragnarok-entur"
            },
    ```
2. Add the entur package:
    ```bash
    composer require ragnarok/ragnarok-entur
    ```
3. Add config from entur and netex packages:
    ```bash
    php artisan vendor:publish --tag=config-entur
    php artisan vendor:publish --provider="TromsFylkestrafikk\Netex\NetexServiceProvider" --tag=config
    ```
4. Add necessary `.env` variables:

    ```ini
    ENTUR_AUTH_URL          = https://partner.staging.entur.org/oauth/token
    ENTUR_CLIENT_ID         = ***
    ENTUR_SECRET            = ***
    ENTUR_AUDIENCE          = https://api.staging.entur.io
    ENTUR_API_PATH          = timetable-admin/v1/timetable/download_netex_blocks/***
    ENTUR_ENV               = staging

    CLEOS_AUTH_URL          = https://partner.staging.entur.org/oauth/token
    CLEOS_CLIENT_ID         = ***
    CLEOS_SECRET            = ***
    CLEOS_AUDIENCE          = https://api.staging.entur.io
    CLEOS_API_PATH          = cleos-reporting/api/v1
    ```

## License

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program. If not, see <https://www.gnu.org/licenses/>.
