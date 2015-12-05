<?php

namespace PhpInPractice\Matters\Aggregate;

interface Repository
{
    /**
     * @param IsEventSourced|object $aggregateRoot
     */
    public function persist($aggregateRoot);

    public function findById($uuid);
}
