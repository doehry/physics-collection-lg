# physics-collection-lg

## About the project

Physics Collection LG is a Wordpress-based administration system for the physics collection at Liechtensteinisches Gymnasium.

The repository contains the entire Wordpress installation including all necessary plugins. The code of the actual project is implemented as a child theme of Twenty Twenty One and is located in the folder wp-content/themes/physics-collection-lg.

## Built with

* [Wordpress 6.8.2](https://wordpress.org)
* [Pods – Custom Content Types and Fields 3.3.2](https://wordpress.org/plugins/pods)
* [WP Extended Search 2.2.1](https://wordpress.org/plugins/wp-extended-search)
* [Wordpress Theme Twenty Twenty One 2.5](https://wordpress.org/themes/twentytwentyone)

## Requirements

Space on a web server, supporting

* PHP 7.4 or greater
* MySQL 5.7 or MariaDB 10.3 or greater
* HTTPS support

## Installation

1. Upload the files to your web server.
2. Create a database for WordPress on your web server, as well as a MySQL (or MariaDB) user who has all privileges for accessing and modifying it.
3. Rename wp-config-sample.php to wp-config.php, then edit the file and add your database information.
4. Run the WordPress installation script by accessing the URL in a web browser.
5. Activate the plugin physics-collection-lg.
6. Change the theme to physics-collection-lg.