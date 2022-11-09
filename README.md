# Web Project S5
L3 Informatique (2022-2023)

**Version Symfony:** 6.1


## Authors

- [Lucas Mailly](https://www.github.com/LucasMailly)
- [Mathis Gaborieau](https://github.com/MathisGV)
- [Fanny Bezançon](https://github.com/fannybezancon)
- [Manon Lacombe](https://www.github.com/ManonLacombe)


## Run Locally

Clone the project

```bash
git clone https://github.com/LucasMailly/s5_web_project
```

Go to the project directory

```bash
cd s5_web_project
```

Modify the environment variables, to connect to the database and add the following variables (set real mailer_dsn and switch to enable if you want to check emails on registration):
```bash
MAILER_DSN=smtp://localhost:1080
MAIL_CONFIRMATION=disable
```

Install dependencies
```bash
composer install
npm install
```

Set up database and load test dataset
```
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate
symfony console doctrine:fixtures:load
```
Load assets
```bash
npm run dev
```

Run web server
```bash
symfony serve
```

## Demo accounts

| email   |      password      | blocked |
|----------|-------------|----------|
| admin@test.com |  123456 | true |
| user1@test.com |  123456 | true |
| user2@test.com |  123456 | true |
| pro1@test.com |  123456 |  true |
| pro2@test.com |  123456 | true |
| pro3@test.com |  123456 | true |
