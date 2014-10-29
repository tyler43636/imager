<?php namespace Atrauzzi\Imager\Storage;

use Atrauzzi\Imager\Model\Image;
use Atrauzzi\Imager\Mime;

class FileNameGenerator {

    /**
     * Generate a unique filename for an image
     *
     * @param Image $image
     * @param array $filters
     * @return string
     */
    public function generateFileName(Image $image, array $filters = []) {

        return sprintf('%s-%s.%s',
            $image->getKey(),
            $this->generateHash($image, $filters),
            Mime::getExtensionForMimeType($image->mime_type)
        );

    }

    /**
     * Generates a hash based on an image and it's filters.
     *
     * @param Image $image
     * @param array $filters
     * @return string
     */
    public function generateHash(Image $image, array $filters = []) {

        $state = [
            'id' => (string)$image->getKey(),
            'filters' => $filters
        ];

        // Must be recursively sorted otherwise arrays with similar keys in different orders won't have the same hash!
        $state = $this->recursiveKeySort($state);

        return md5(json_encode($state));

    }

    /**
     * Utility method to ensure that key signatures always appear in the same order.
     *
     * @param array $array
     * @return array
     */
    protected function recursiveKeySort(array $array) {

        ksort($array);

        foreach($array as $key => $value)
            if(is_array($value))
                $array[$key] = $this->recursiveKeySort($value);

        return $array;
    }

}
