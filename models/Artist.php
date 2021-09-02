<?php 

class Artist extends Model
{
    public function getAlbums()
    {
        return $this->hasMany("Album");
    }

}

class Album extends Model{
    public function getTracks()
    {
        return $this->hasMany("Track");
    }

}

class Track extends Model
{
}
