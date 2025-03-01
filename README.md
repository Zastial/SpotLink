# SpotLink


# Install

At first, run this command to initialiaze composer
```
composer install
```

# Database

- Install PostgreSQL
- Run those commands :
```
CREATE DATABASE "SPOTLINK" WITH ENCODING 'UTF8';

CREATE USER spotlink WITH PASSWORD 'spotlink';

GRANT ALL PRIVILEGES ON DATABASE "SPOTLINK" TO spotlink;

ALTER DATABASE "SPOTLINK" OWNER TO spotlink;
```

To create a migration :
```
php bin/console make:migration
```

To play the migrations :
```
php bin/console doctrine:migrations:migrate
```

Then launch the app :

```
symfony server:start
php bin/console server:run
php -S 127.0.0.1:8000 -t public/
```

To Run Tailwind watcher :

```
php bin/console tailwind:build --watch
```

postgres
net start postgresql



# Authentification

Nous utilisons le JWT pour s'authentifié.
Dans le security.yml sont indiqués les rôles nécessaire pour pouvoir accès aux différentes routes

* Le service `JwtAuthenticator` permet de gérer l'authentification via le JWT. Il intervient avant d'accéder aux controlleurs et permet ainsi de sécurisé l'accès aux ressources.

* Le service `JwtService` permet de gérer la création et validation des tokens JWT avec les différents claims pour notre application. 
