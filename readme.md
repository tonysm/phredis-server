This is a playground project. It has the basic implementations of the [Redis protocol](https://redis.io/topics/protocol) in PHP using the [Amphp](https://amphp.org/) async library.

## Running

- Install dependencies `composer install`
- Start the Phredis Server `php bin/phredis.php {port=6378}`
- Run the _redis-cli_ to interact with it `redis-cli -p {port}`

You can even run the _redis-benchmark_ if you want to, just make sure you only test the set command:

- Run `redis-benchmark -t set -p {port}`