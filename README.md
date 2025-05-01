# FIFI

FIFI is a PHP framework design to ease and simplify the deelopment of small to mid level web applications or api.

Based on the MVC architecture, It reuses some well-known functionnalities and naming from the Laravel framework such as `model::findAll`or `model::create` and more.

It also implements a small CLI giving the user the ability to run database migrations from command line.

# Run
To run the project you need to have docker installed then run the command at the root of you project
```bash
docker-compose up --build
```

then navigate to localhost:9000


## Model

## Controller
### Middleware

```php
$router->get('/api/user/me', function ($params) {
  UserController::read($params);
}, ["AuthMiddleware", "index"]);
```

## View



## Routing

```php
  $router->get('/', function () {
    return view('welcome');
  });

  $router->post('/artist', function () {
    $model = new Artist();
    $model->fromJson();
    $model->save();
  });

  $router->get('/api/user/me', function ($params) {
    UserController::read($params);
  }, ["AuthMiddleware", "index"]);

```

## Database

database informations should be added in a .env file at the root of the app folder. with the folowing information as shown in the .env.sample file.
This file is excluded from push actions and is amended programmatically during the release with the predefined Github secrets.
You will save the staging and production database credentials in github settings as follow:

```
*    TABLE_PREFIX
*    DATABASE_HOST
*    DATABASE_NAME
*    DATABASE_USER
*    DATABASE_PASSWORD
```

## Migration

`php fifi migrate`
THis command will execute the sql script within the `db.sql`file in the database folder. In this file you will define your sql schema.
Naming convention:
Your table name should be identical as your class Model name.

## CD/CI

FIFI gives you the abiliy to deploy you changes via FTP directly to the server with his built in github action. You can also implement a staging and production environmenrt by creating environments in your github settings by creating those secrets:

```
*    FTP_SERVER
*    FTP_USERNAME
*    SERVER_DIR
*    FTP_PASSWORD
```

### How to release

Your project can be deployed in two differents enviroments defined in your environment project settings, `staging` and `producion`

#### staging

To release in your staging environment, you can run the following command:

```#example
git tag prod.20221201.v0
git push tags
```

#### production

the default release system uses tags but you can customize the github action file with the specific trigger you'll want.
The following commands

```bash
#example
git tag prod.20221201.v0
git push tags
```

will release the production version?
