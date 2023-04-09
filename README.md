# Installation

## Basic Requirements

To setup this project, follow to following step:
- PHP >= 8.0
- Composer
- MySQL >= 5.7

## Setup guide
- ### Step 1

    copy .env.exapmle to .env
- ### Setp 2

Create a Empty database and update following database details

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_database
DB_USERNAME=my_username
DB_PASSWORD=my_password
```

- ### Step 3

Install composer using following command

```sh
composer install
```
- ###  Step 4

Run database migration to migrate database stracture

```sh
php artisan migrate --seed

```

- ### Step 5

Once database migration is done, you are ready to test the project. Use following command to run the project

```
php artisan serve
```
This command will run the project on [http://127.0.0.1:8000]


```
```

# Guide to Test APIs

Find the Postman collection of APIs

[PostnamCollection](https://drive.google.com/file/d/15bcEta91w5Ibf67QXKADkWXAwGOSzBoB/view?usp=share_link)

Here is some screenshots of API response

[Screenshot1](https://drive.google.com/file/d/1f7dbvvgNZjK4ImaRDBk44pq3oO4IYC-N/view?usp=share_link)

[Screenshot2](https://drive.google.com/file/d/1mN2BSBPjzrABm-7dcgHBNS9Z91D9bzWF/view?usp=share_link)

[Screenshot3](https://drive.google.com/file/d/1fi_ZD6f2nOJJyMXYUnfioG1NvD5kMd8i/view?usp=share_link)
