# Iis
Project for Information Systems class.

## Authors:
- Jiří Prokop
- Patrik Čerbák
- Radek Janečka


## Dev setup
1st install symfony and needed stuff.
https://symfony.com/doc/current/setup.html

<!-- this step shouldn't be requited, because it should be in composer.json so 'composer install' should do the trick -->
<!-- Then doctrine.
https://symfony.com/doc/current/doctrine.html#installing-doctrine -->

Install the db software on your pc.
For fedora its here: https://docs.fedoraproject.org/en-US/quick-docs/installing-mysql-mariadb/

Creating the db.
`symfony console doctrine:database:create`

Destroing the db.
`symfony console doctrine:database:drop`
