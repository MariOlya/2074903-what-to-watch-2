<p align="center">
              <a>
                <span>W</span>
                <span>T</span>
                <span>W</span>
              </a>
        <h1 align="center">What To Watch</h1>
        <h3 align="center">study project by Olga Marinina</h3>

<p align="center">
<img src="https://img.shields.io/badge/php-%5E8.1-blue">
<img src="https://img.shields.io/badge/laravel-%5E10.0-red">
<img src="https://img.shields.io/badge/mysql-8.0-orange">

* Student: [Olga Marinina](https://up.htmlacademy.ru/yii/4/user/2074903).
* Mentor: [Mikhail Selyatin](https://htmlacademy.ru/profile/id919955).

### About project

"What to watch" is a new generation online cinema.
Watch the latest movies or series episodes absolutely free and in the best quality.
Leave reviews, rate and choose only the best from the world of big cinema.

### API specification

Check it [here](https://10.react.pages.academy/wtw/spec#get-/films).

### Getting started

For start with this project you should use the following command:
```
./vendor/bin/sail up
```
Then for using short command you should create alias:
```
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```
And now you can do the first command with: 
```
sail up
```

### Migrations

All db migrations were prepared, so just run:

```
sail artisan migrate
```

### Data

#### 1. Strict data

In this project we have default set data for file type, film status, user role, link type.
So these data will be needed for you any way, so they were added in migrations

#### 2. Fake data

To test smth and check website operation you should add fake data.
They were prepared too, soo just run:

```
sail artisan db:seed
```

### Tests

We work here with PHPUnit. To start tests just run:

```
sail artisan test
```
For concrete functional test:
```
sail artisan test --filter=<name>  
```
