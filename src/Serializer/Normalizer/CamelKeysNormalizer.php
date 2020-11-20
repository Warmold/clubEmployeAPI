<?php

namespace App\Serializer\Normalizer;

/**
 * Class CamelKeysNormalizer
 */
class CamelKeysNormalizer
{
    /**
     * @param array $data
     *
     * @return array
     *
     * @throws \LogicException
     */
    public function normalize(array $data): array
    {
        $this->normalizeArray($data);

        return $data;
    }

    /**
     * Normalizes an array.
     *
     * @param array &$data
     *
     * @throws \LogicException
     */
    private function normalizeArray(array &$data): void
    {
        $normalizedData = array();

        foreach ($data as $key => $val) {
            $normalizedKey = $this->normalizeString($key);

            if (($normalizedKey !== $key) && \array_key_exists($normalizedKey, $normalizedData)) {
                throw new \LogicException(sprintf(
                    'The key "%s" is invalid as it will override the existing key "%s"',
                    $key,
                    $normalizedKey
                ));
            }

            $normalizedData[$normalizedKey] = $val;
            $key = $normalizedKey;

            if (\is_array($val)) {
                $this->normalizeArray($normalizedData[$key]);
            }
        }

        $data = $normalizedData;
    }

    /**
     * Normalizes a string.
     *
     * @param string $string
     *
     * @return string
     */
    protected function normalizeString($string): string
    {
        if (false === strpos($string, '_')) {
            return $string;
        }

        return preg_replace_callback('/_([a-zA-Z0-9])/', static function ($matches) {
            return strtoupper($matches[1]);
        }, $string);
    }
}