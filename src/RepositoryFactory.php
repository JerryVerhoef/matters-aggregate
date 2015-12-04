<?php

namespace PhpInPractice\Matters\Aggregate;

interface RepositoryFactory
{
    /**
     * @param string $aggregateType
     * @return Repository
     */
    public function create($aggregateType);
}
