<?php

namespace PhpInPractice\Matters\Aggregate;

interface StreamNameGenerator
{
    public function generate($aggregateName, $id);
}
