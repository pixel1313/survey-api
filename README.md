Setup and Installation
======================

Prerequisites
-------------
You'll need to install PHP, a database (MariaDB, MySQL, or PostgreSQL), git, the symfony cli, and composer.

TODO: list basic install instructions and link to more detailed sources.

Environment Configuration
-------------------------

Don't forget to create a `.env.local` file to confiure the database url.
Replace USERNAME and PASSWORD with the correct values.

```bash
DATABASE_URL="mysql://USERNAME:PASSWORD@127.0.0.1:3306/survey-api?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```


Environment Installation
------------------------

Now we need to run som ecommands to set up the development environment:

```bash
# This will create the configured database for you (db_name)
symfony console doctrine:database:create

# This will execute all of the migrations that have not yet been run against your database. When dealing with a fresh database, it will run all of the migrations in the migrations folder.
symfony console doctrine:migrations:migrate

# This will load the data fixtures into the database so you have some test data to work with.
symfony console doctrine:fixtures:load
```

Running the Development Web Server
----------------------------------

Run the development web server to test the code out. Running the server in the background allows you to keep issuing commands on the same terminal. Sometimes its useful to run the server in the foreground to diagnose some issues, so that command is included.

```bash
# Run the server in the background:
symfony serve -d

# Stop the server in the background:
symfony server:stop

# Run the server in the foreground:
symfony serve
```

Development and Processes
=========================

Creating or Editing an Entity
-----------------------------

```bash
# The following command will generate the class and any fields you need. It can also be used to edit an entity.
symfony console make:entity
```

Afterwards you'll have an entity class file in `src\App\Entity\EntityName.php` with the relevant fields, getters/setters, and attributes defining any ORM type information, relations, or limits.
After creating, editing, or removing any antities, you'll need to save a migration with the new database schema with the following command:

```bash
# Generate a new database migration
symfony console make:migration
```

And anytime you pull a new migration from git or create a new one, you'll need to apply the migration to your database.

```bash
# Apply migrations
symfony console doctrine:migrations:migrate
```


Manual Database Inspection
--------------------------

To perform a direct database query you can make use of the doctrine query command:

```bash
symfony console doctrine:query:sql 'SELECT * FROM entity_table'
```


Advanced Development Topics
===========================

Api-Platform State Processors
-----------------------------

[State processors](https://api-platform.com/docs/core/state-processors/) are how we're dynamically setting the owner of a created survey to the owner that issued the create survey request.

```bash
# Create State Processor
symfony console make:state-processor
```

Example:

```php
<?php

namespace App\Entity;

use ApiPlatform\Metadata\Post;
use App\State\BlogPostProcessor;

#[Post(processor: BlogPostProcessor::class)]
class BlogPost {}
```

Serialization Context
---------------------

It's possible to use all of the attributes from the Symfony Serializer component within the entities/resources we've defined.

We can change the normalization and denormalization context at the same time, in this case to format the date a specific way:

```php
<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity]
#[ApiResource]
class Book
{
    #[ORM\Column]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    public ?\DateTimeInterface $publicationDate = null;
}
```

We can also change only one or the other using the context attribute:

```php
<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity]
#[ApiResource]
class Book
{
    #[ORM\Column]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    public ?\DateTimeInterface $publicationDate = null;
}
```


Serialization Dynamic Context
-----------------------------

TODO: pull relevant information from [This Link](https://api-platform.com/docs/core/serialization/#changing-the-serialization-context-dynamically).