<?php

namespace Corpsoft\Logging\Helpers;

class GetFilteredAndFormattedStack
{
    /**
     * @param array $callStack
     * @return array
     */
    public function __invoke(array $callStack): array
    {
        $filteredStack =  array_filter($callStack, function ($call) {
            $excludedClasses = [
                'Illuminate',
                'Corpsoft\Logging',
                'Symfony\Component'
            ];

            if (isset($call['class'])) {
                foreach ($excludedClasses as $excludedClass) {
                    if (str_contains($call['class'], $excludedClass)) {
                        return false;
                    }
                }
            }
            return true;
        });

        array_filter(
            (array)array_walk($filteredStack, function (&$call) {
                if (array_key_exists('class', $call)
                    && array_key_exists('line', $call)
                    && array_key_exists('function', $call)
                ) {
                    $call = "*Class:* " . $call['class'] . " \n *Function:* " . $call['function'] . " \n *Line:* " . $call['line'];
                } else {
                    $call = null;
                }
            })
        );

        return $filteredStack;
    }
}
