# Mastori API

## Installation

A working installation of Apache 2.4 or higher with PHP 5.6 or higher,
and MySQL 5.6 or higher (for geospatial queries to work) is required for this application to work properly.

__Install Composer__ if you don't have it installed already.

    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer

__Clone Mastori API__ to a directory accessible by the web server. eg. `/var/www`

    git clone https://github.com/fgiannar/mastori-api.git

__Install dependencies, configure permissions, and configure databse__

To avoid any issues with the `artisan` cli tool, the `.env` mysql host should be
set to `127.0.0.1` instead of 'localhost'. If the mysql is installed on a separate
server this is not required.

    cd mastori-api
    composer install
    chmod -R 777 storage
    cp .env.example .env
    vi .env # set at least database values (also FACEBOOK_ID and FACEBOOK_SECRET for FB related functionalities to work)
    php artisan key:generate
    php artisan migrate:refresh --seed
    php artisan php artisan l5-swagger:publish  (to publish swagger docs in api/documentation path)

__Configure Web Server__ to proper root directory

At this point the application is ready. All we need is to point our web server
to the correct directory, eg `/var/www/mastori-api/public`, and restart our web server.

Last thing is to make sure that Apache's `mod_rewrite` is enabled and that
`AllowOverride` is set to `All` for our directory.

    a2enmod rewrite
    service apache2 restart

__SEEDING__

    php artisan db:seed

Will populate the DB with 500 mastoria and 500 end users. Both mastoria and end users will have random addresses.
Mastoria will also have random professions and areas they serve.
Areas are populated based on the _greece-prefectures.geojson_ found in database/seeds/data.
Professions are populated based on the _professions.json_ found in database/seeds/data.