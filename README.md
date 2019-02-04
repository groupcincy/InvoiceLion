# InvoiceLion

[![Join the chat at https://gitter.im/Usecue/InvoiceLion](https://badges.gitter.im/Usecue/InvoiceLion.svg)](https://gitter.im/Usecue/InvoiceLion?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Software developers during the day and "Invoice Lions" at night... We are twin brothers and freelance software developers with a hobby: We like to build online tools and we like to share. 
We needed an Invoicing system and decided to build our own. Invoicing should be open and free. We use InvoiceLion ourselves, and continuously improve this tool. We aspire to be the WordPress of Invoicing, so we would love to get feedback from you. Let's build this tool together! Are we doing a good job? What needs to be improved (first) or added? [Let us know](https://gitter.im/Usecue/InvoiceLion)!

## InvoiceLion.com

We run a production instance of this software on [InvoiceLion.com](https://www.invoicelion.com) and you can use it for FREE.
We promise to never make a profit from this service, but we may ask for a (optional) donation in order to pay for our servers.

## Download

[Grab the latest release](https://github.com/Usecue/InvoiceLion/releases) of this software and install it on your own server. It is 100% free and open souce (MIT licensed).

## Requirements

- PHP 7 with `php-mysql`, `php-mbstring` and `php-dom` extensions enabled
- MariaDB 10.1 (or MySQL 5.6) or higher

## Installation

- unzip the source code
- run `composer install` to install dependencies
- create the database using `create_db.sql` (adjust the database credentials)
- initiate the database using `db_structure.sql`
- copy `config/config.php.template` to `config/config.php` and fill in the database credentials

## Running

Point the `DocumentRoot` of Apache (or `root` of Nginx) to the `web` folder of the project.

## Support

Please use the software, report bugs and ask for features using the [Github issues](https://github.com/Usecue/InvoiceLion/issues)!

NB: No costs and no guarentees. Please be kind to each other and help each other out!
