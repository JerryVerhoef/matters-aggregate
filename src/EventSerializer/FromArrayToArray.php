<?php

namespace PhpInPractice\Matters\Aggregate\EventSerializer;

use PhpInPractice\Matters\Aggregate\EventSerializer;

final class FromArrayToArray implements EventSerializer
{
    public function serialize($object)
    {
        $methodName = 'toArray';
        $this->assertMethodExists($object, $methodName);

        return call_user_func([$object, $methodName]);
    }

    public function unserialize($class, array $data)
    {
        $methodName = 'fromArray';
        $this->assertMethodExists($class, $methodName);

        return call_user_func([$class, $methodName], $data);
    }

    /**
     * @param string|object $object
     * @param string        $methodName
     */
    private function assertMethodExists($object, $methodName)
    {
        if (! method_exists($object, $methodName)) {
            $className = is_string($object) ? $object : get_class($object);
            throw new MissingMethodException(
                'Method "' . $methodName . '" does not exist on class/object "' . $className . '"'
            );
        }
    }
}
