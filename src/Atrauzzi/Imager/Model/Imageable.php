<?php namespace Atrauzzi\Imager\Model;


interface Imageable {

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function images();

}
