<?php
namespace KDuma\Eloquent;

use Config;
use Hashids\Hashids;

/**
 * Class Tokenable
 * @package KDuma\Eloquent
 */
trait Tokenable {

    /**
     * @return Hashids
     */
    private function getHashingInstance(){
        $salt = Config::get('app.key') . ($this->salt?:$this->getTable());
        $min_hash_length = $this->length?:10;
        $alphabet = $this->alphabet?:'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        return new Hashids($salt, $min_hash_length, $alphabet);
    }

    /**
     * @return string
     */
    public function getHashidAttribute()
    {
        $hashids = $this->getHashingInstance();
        return $hashids->encode($this->id);
    }

    /**
     * @param $query
     * @param $hashid
     * @return bool|int
     */
    public function scopeWhereHashid($query, $hashid)
    {
        $hashids = $this->getHashingInstance();
        $id = $hashids->decode($hashid);

        if(count($id) == 0)
            return false;

        return $query->where('id', $id[0]);
    }
}
