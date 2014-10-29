<?php namespace Atrauzzi\Imager\Processing;

use Symfony\Component\HttpFoundation\File\File;
use Atrauzzi\Imager\Model\Image;


interface Filter {

	public function process(File $file, Image $image);

}