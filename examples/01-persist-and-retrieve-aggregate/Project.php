<?php

use PhpInPractice\Matters\Aggregate\IsEventSourced;
use Rhumsaa\Uuid\Uuid;

class Project
{
    use IsEventSourced;

    private $id;
    private $name;

    /**
     * @param $name
     *
     * @return Project
     */
    public static function create($name)
    {
        $project = new Project();
        $project->recordThat(new ProjectCreated((string)Uuid::uuid4(), $name));

        return $project;
    }

    public function name()
    {
        return $this->name;
    }

    public function id()
    {
        return $this->id;
    }

    public function rename($name)
    {
        $this->recordThat(new ProjectRenamed($this->id, $name));
    }

    private function whenProjectCreated(ProjectCreated $event)
    {
        $this->id = $event->id();
        $this->name = $event->name();
    }

    private function whenProjectRenamed(ProjectRenamed $event)
    {
        $this->name = $event->name();
    }
}
