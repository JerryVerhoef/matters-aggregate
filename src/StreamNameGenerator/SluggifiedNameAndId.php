<?php

namespace PhpInPractice\Matters\Aggregate\StreamNameGenerator;

use PhpInPractice\Matters\Aggregate\StreamNameGenerator;

/**
 * This Stream name generator will convert the provided name into a sluggified version of the aggregate name
 * and concatenate the id using a hyphen.
 *
 * This strategy is especially well-suited for passing a class name (including namespace) as name for the
 * aggregate as all slashes will be replaced with dots and the name will be lowercased.
 *
 * For example:
 *
 *     Given that the aggregate name is 'PhpInPractice\Project' and the id is
 *     '273b55ba-1286-42b4-97d4-add415e798a6' (a UUID) then the generated name will become
 *     'phpinpractice.cid.project-273b55ba-1286-42b4-97d4-add415e798a6'.
 *
 * This strategy will strip all slashes and whitespace characters from the start and end of the aggregate name
 * so that it doesn't matter whether you provide 'PhpInPractice\Project' (a QCN) or '\PhpInPractice\Project'
 * (a FQCN), it will be treated as the same aggregate.
 */
final class SluggifiedNameAndId implements StreamNameGenerator
{
    /**
     * Returns a sluggified version of the name combined with the id, separated by a hyphen.
     *
     * @param string $aggregateName
     * @param string $id
     *
     * @return string
     */
    public function generate($aggregateName, $id)
    {
        return $this->sluggifyAggregateName($aggregateName) . '-' . $id;
    }

    /**
     * Makes a slug out of the given name by making it lowercase and replacing any slashes with a dot.
     *
     * I have explicitly chosen not to use the hyphen or underscore as a separator for the name as this
     * character is also used by Eventstore as the 'category' separator.
     *
     * @param string $aggregateName
     *
     * @return string
     */
    private function sluggifyAggregateName($aggregateName)
    {
        return strtolower(str_replace(['\\', '/'], '.', trim($aggregateName, "\\ /\t\n")));
    }
}
