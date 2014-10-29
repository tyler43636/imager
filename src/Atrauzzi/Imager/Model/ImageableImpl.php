<?php namespace Atrauzzi\Imager\Model;


trait ImageableImpl {

	public function images() {
		return $this->morphMany('Atrauzzi\Imager\Model\Image', 'imageable');
	}

}