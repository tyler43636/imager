<?php

use Atrauzzi\Imager\Model\Image as ImageModel;

class ImageFactory {

    /**
     * Generate fake attributes for an image
     *
     * @param array $overrides
     * @return array
     */
    public function getFakeImageAttributes(array $overrides = array())
    {
        $imageAttr = [
            'height' => 200,
            'width'  => 300,
            'slot'   => '',
            'mime_type' => 'image/jpeg',
            'average_color' => '000000'
        ];

        return array_merge($imageAttr, $overrides);
    }

    /**
     * Generate an image model without saving it
     *
     * @param array $overrides
     * @return ImageModel
     */
    public function getFakeImageModel(array $overrides = [])
    {
        $attributes = $this->getFakeImageAttributes($overrides);

        return new ImageModel($attributes);
    }

    /**
     * Create and save a specified number of images to the db
     *
     * @param $number
     * @param $overrides
     */
    public function saveNumberOfFakeImages($number, array $overrides = [])
    {
        for($i = 0; $i < $number; $i++)
        {
            $image = $this->getFakeImageModel($overrides);
            $image->save();
        }
    }
}
