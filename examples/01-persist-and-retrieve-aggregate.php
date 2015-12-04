<?php
include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/01-persist-and-retrieve-aggregate/ProjectCreated.php';
include __DIR__ . '/01-persist-and-retrieve-aggregate/ProjectRenamed.php';
include __DIR__ . '/01-persist-and-retrieve-aggregate/Project.php';

// Setup the Gateway that can read and write to/from streams
$eventStore = new \EventStore\EventStore('192.168.99.100:2113');

// Instantiate a factory with which we can retrieve Repositories for specific Aggregates
$factory = new \PhpInPractice\Matters\Aggregate\RepositoryFactory($eventStore);

// Create the Repository for the Project aggregate
$repository = $factory->create(Project::class);

// Create a new Project Aggregate
$project = Project::create('TestProject');

// Persist the Aggregate to the Eventstore
$repository->persist($project);

// Rename the project to trigger another event
$project->rename('TestProject2');

// Persist the Aggregate again to the Eventstore
$repository->persist($project);

// Retrieve the aggregate from the Eventstore
$project2 = $repository->findById($project->id());

var_dump($project2);
