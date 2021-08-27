<?php

use Core\DataAccess;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require __DIR__ . '/core/Globals.php';

return ConsoleRunner::createHelperSet(EntityService::em());