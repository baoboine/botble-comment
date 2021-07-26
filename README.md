<p align="center"><a href="https://suyt.tech" target="_blank"><img src="https://suyt.tech/comment2.png" width="800"></a></p>

# Botble plugin comment

This is a plugin for Botble CMS so you have to purchase Botble CMS first to use this plugin.

Demo: https://suyt.tech/which-company-would-you-choose#bb-comment

## Install

- For developers:
    - Rename folder `botble-comment-main` to `comment`.
    - Copy folder `comment` into `/platform/plugins`.
    - Run `composer update` to install Passport.
    - Run `php artisan migrate` to update the database.
    - Run `php artisan passport:install` to generate keys for Passport.
    - Run command `php artisan cms:plugin:activate comment` to activate this plugin.

- For non-developers:
    - Rename folder `botble-comment-main` to `comment`.
    - Copy folder `comment` into `/platform/plugins`.
    - Or go to Admin Panel -> Plugins and activate plugin Comment.

## Usage

### Auto embed
- Go to Admin -> Setting -> Comments to enable comment.

### Manually
- Insert shortcode to your post's content:
``` php
[comment][/comment]
```
