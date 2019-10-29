# README

This project benchmarks the most popular & feature rich PHP serializers. It measures the time consumed during the 
serialization of an object graph and give you a report of the execution.

The result of the benchmark is directly available on travis: https://travis-ci.org/php-serializers/ivory-serializer-benchmark

This repository is a fork of [egeloen/ivory-serializer-benchmark](https://github.com/egeloen/ivory-serializer-benchmark),
the project was looking not maintained for a while, please refer to this as the next reference point when benchmarking
PHP serializers.

## Documentation

If you're interesting to use the project locally, follow the next steps.

### Set up the project

The easiest way to set up the project is to install [Docker](https://www.docker.com) and
[Docker Composer](https://docs.docker.com/compose/) and build the project. The configuration is shipped with a 
distribution environment file allowing you to customize your current user/group ID:

``` bash
$ cp .env.dist .env
```

**The most important part is the `USER_ID` and `GROUP_ID` which should match your current user/group.**

Once you have configured your environment, you can build the project:

``` bash
$ docker-compose build
```

### Install dependencies

Install the dependencies via [Composer](https://getcomposer.org/):

``` bash
$ docker-compose run --rm php composer install
```

### Benchmark

We use [PHPBench](https://phpbench.readthedocs.io/) internally, with sane defaults setup for benchmarking the serializers.

To benchmark serialization, you can use:

``` bash
$ docker-compose run --rm php ./vendor/bin/phpbench run --report=bench
```

By default, the benchmark runs 5 [Revolutions](https://phpbench.readthedocs.io/en/latest/writing-benchmarks.html#improving-precision-revolutions) and 5 [Iterations](https://phpbench.readthedocs.io/en/latest/writing-benchmarks.html#verifying-and-improving-stability-iterations).
You can override either with the `iterations` and `revs` options.

``` bash
$ docker-compose run --rm php ./vendor/bin/phpbench run --report=bench --iterations=10 --revs=10
```

By default, the benchmark runs different configurations of horizontal and vertical complexity.
If you wish to specify your own, you can use the `parameters` option  which is an array of two 
integers, the first representing the horizontal and the second representing the vertical complexity.

``` bash
$ docker-compose run --rm php ./vendor/bin/phpbench run --report=bench --parameters='[1,2]'
```

If you want to run the benchmark only for a specific or subset of serializers, you can use the `filter` option:

``` bash
$ docker-compose run --rm php ./vendor/bin/phpbench run --report=bench --filter=Symfony
```

### Available serializers

You can see a list of the serializer available and its current version by running the following command:

``` bash
$ docker-compose run --rm php ./vendor/bin/phpbench info
```

Available implementations:

* `BetterSerializer`
* `Ivory`
* `JaneAutomapper`
* `Jms`
* `JmsMinimal`
* `JsonSerializable`
* `SerializardClosure`
* `SerializardReflection`
* `SymfonyGetSetNormalizer`
* `SymfonyObjectNormalizer`
* `TSantos`
* `Gson`


## Contribute

We love contributors! PhpSerializers is an open source project. If you'd like to contribute, feel free to propose a PR!.

## License

The Php Serializers Benchmark is under the MIT license. For the full copyright and license information, please read the
[LICENSE](/LICENSE) file that was distributed with this source code.
