
A PHP syntax highlighting function (for PHP code), that relies on the PHP tokenizer.

Tokens are wrapped between span tags with camelCased token names as class attributes. Internal PHP functions are also wrapped in links that point to the PHP docs.

Requires PHP 5.3+ !
  
Usage:
______

    print highlight($code);
 

Check out the index.php file for a full example ;)
