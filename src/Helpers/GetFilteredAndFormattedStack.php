<?php

namespace Corpsoft\Logging\Helpers;

class GetFilteredAndFormattedStack
{
    /**
     * @param array $callStack
     * @param bool $line
     * @return array
     */
    public function __invoke(array $callStack, bool $line = true): array
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
            (array)array_walk($filteredStack, function (&$call) use ($line) {
                if (array_key_exists('class', $call)
                    && array_key_exists('line', $call)
                    && array_key_exists('function', $call)
                ) {
                    $callLine = $line ? " \n *Line:* " . $call['line'] : "";
                    $call = "*Class:* " . $call['class'] . " \n *Function:* " . $call['function'] . $callLine;
                } else {
                    $call = null;
                }
            })
        );

        return $filteredStack;
    }
}
