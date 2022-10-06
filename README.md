### Starting Docker Compose

```
docker-compose up -d
```

### Using Composer

`docker-compose run --rm composer <cmd>`

Where `cmd` is any of the available composer command.

## how to start project

```
1. docker-compose up -d
2. docker-compose run --rm composer update
3. docker-compose exec php php artisan migrate
4. docker-compose exec php php artisan db:seed
```
