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

CREATE USER spotlink;

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
```

To Run Tailwind watcher :

```
php bin/console tailwind:build --watch
```