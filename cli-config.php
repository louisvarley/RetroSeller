<?php

use Core\DataAccess;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use \Core\Services\EntityService as Entities;

require __DIR__ . '/core/Globals.php';

return ConsoleRunner::createHelperSet(Entities::em());