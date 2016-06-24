soccerbot
=========

This is a Facebook Groups bot that helps organize pickup games. In the pickup soccer group I'm in, it's often hard to tell whether there's enough interest in a game or who exactly is interested in going. This bot helps out by keeping track of who's interested in a game and posting a comment alerting everyone once a minimum number of people has been achieved.

The bot is written with PHP 7 and uses Facebook's PHP SDK with, of course, the Facebook Graph API (v2.6). Composer manages the dependencies.

### Setup

1. Clone/download this repository

2. Create a Facebook app: https://developers.facebook.com/ Note the `App ID` and the `App Secret`.

3. Create a Facebook account for your bot.

4. Add your bot as a developer to your Facebook app.

5. From the Facebook account for the bot, [generate an access token](https://developers.facebook.com/tools/explorer) using the Facebook Graph API explorer. **Make sure to generate a token with the `publish_actions` and `user_managed_groups` permissions.**

6. Create a MySQL database.

7. Fill out `lib/Config.php.CHANGEME` and then rename the file to `lib/Config.php`.

8. [Install Composer](https://getcomposer.org/download/):

    ```
    curl -sS https://getcomposer.org/installer | php
    ```
    
9. Install dependencies

    ```
    php composer.phar install
    ```
    
10. Fill out database information in `phinx.yml.CHANGEME` and then rename the file to `phinx.yml`.
    
11. Run database migrations

    ```
    php vendor/bin/phinx migrate
    ```
    
12. Run the bot
  
    ```
    php main.php
    ```
    
### TODO
* Add more commands
* Clean up code
* Better exceptions

## License

     The MIT License (MIT)

     Copyright (c) 2016 Tony Peng

     Permission is hereby granted, free of charge, to any person obtaining a copy of
     this software and associated documentation files (the "Software"), to deal in
     the Software without restriction, including without limitation the rights to
     use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
     the Software, and to permit persons to whom the Software is furnished to do so,
     subject to the following conditions:

     The above copyright notice and this permission notice shall be included in all
     copies or substantial portions of the Software.

     THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
     FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
     COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
     IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
     CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.