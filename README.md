# Botble plugin comment

## Install

- Clone and copy to your plugins folder
- Rename comment folder
- Before enable you need to makesure Laravel Passport is installed or just update
``` bash
$ composer update
```
- Install passport if this is your first time
```bash
# update passport database
$ php artisan migrate 
# install key
$ php artisan passport:install 
```

### Important
If your theme is using PurgeCss you need to add this to your webpack.mix.js Or extend it
```js
purgeCss({
    content: [
        'platform/plugins/comment/resources/views/**/*.blade.php',
        "platform/plugins/comment/resources/assets/js/components/*.vue",
        "platform/plugins/comment/resources/assets/js/components/**/*.vue",
    ],
})
```
Then, you need to compile your source again
```bash
$ npm run production
```

## Usage
- Insert to your Article with ShortCode:
``` php
[comment][/comment]
```
- Or auto embed to your article: Setting > Comment
