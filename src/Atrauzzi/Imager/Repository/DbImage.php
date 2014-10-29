<?php namespace Atrauzzi\Imager\Repository;

use Atrauzzi\Imager\Model\Image as ImageModel;
use Atrauzzi\Imager\Model\Imageable;


class DbImage implements Image {

	/**
	 * Creates a new Image object in the database.
	 *
	 * @param $attributes
	 * @return \Atrauzzi\Imager\Model\Image
	 */
	public function create($attributes) {
		return ImageModel::create($attributes);
	}

	/**
	 * Gets an image object by it's id.
	 *
	 * @param int $id
	 * @return \Atrauzzi\Imager\Model\Image
	 */
	public function getById($id) {
		return ImageModel::find($id);
	}

	/**
	 * @param $slot
	 * @param Imageable $imageable
	 * @return \Atrauzzi\Imager\Model\Image
	 */
	public function getBySlot($slot, Imageable $imageable = null) {

		if($imageable)
			$query = ImageModel::forImageable(get_class($imageable), $imageable->getKey());
		else
			$query = ImageModel::unattached();

		return $query->inSlot($slot)->first();

	}

}