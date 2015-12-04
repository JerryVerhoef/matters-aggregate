<?php

namespace PhpInPractice\Matters\Aggregate;

interface Repository
{
    /**
     * @param IsEventsourced|object $aggregateRoot
     */
    public function persist($aggregateRoot);

    public function findById($uuid);
}
