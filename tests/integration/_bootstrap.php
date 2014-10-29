<?php
// Here you can initialize variables that will be available to your tests

require "tests/_support/SetupDatabase.php";

SetupDatabase::setupTestDb();

\Codeception\Module\Dbh::$dbh = SetupDatabase::getPdo();
