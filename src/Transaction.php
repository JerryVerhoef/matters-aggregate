<?php

namespace PhpInPractice\Matters\Aggregate;

interface Transaction
{
    public function push($streamUri, array $writeableEvents);
    public function commit();
    public function rollback();
}
