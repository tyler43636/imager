<?php

use Illuminate\Database\Eloquent\Model;
use Atrauzzi\Imager\Model\Imageable;
use Atrauzzi\Imager\Model\ImageableImpl;

class Album extends Model implements Imageable {

    use ImageableImpl;

    protected $fillable = [
        'name'
    ];
}
