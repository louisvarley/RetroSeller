<?php

use Core\DataAccess;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require __DIR__ . '/Core/Globals.php';

return ConsoleRunner::createHelperSet(entityManager());