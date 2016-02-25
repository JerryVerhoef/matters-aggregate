<?php
namespace PhpInPractice\Matters\Aggregate;

interface MetadataStore
{
    /**
     * Returns the metadata that needs to be registered with this event.
     *
     * @param object $event
     *
     * @return string[]
     */
    public function metadata($event);
}
