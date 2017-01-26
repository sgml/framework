<?php

namespace Arc\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'posts';

    protected $primaryKey = 'ID';

    /**
     * Adds a Post Meta row (or rows) with the given key and value (or array of key value pairs)
     *
     * @param mixed $data Metadata key/name or array of key value pairs
     * @param mixed $value Metadata value. Must be serializable if non-scalar, ignored if $key is array
     * @param bool $unique (optional) Whether the same key should not be added
     * @return mixed|false Meta ID on success for single, array of ids for array input, false on failure
     **/
    public function addMeta($data, $value = null, $unique = false)
    {
        if (!is_array($data)) {
            $data = [
                $data => $value
            ];
        }

        foreach ($data as $key => $value) {
            $results[] = add_post_meta($this->ID, $key, $value, $unique);
        }

        if (count($results) == 1) {
            return $results[0];
        }

        return $results;
    }

    /**
     * Adds a unique Post Meta row (or rows) with the given key and value (or array of key value pairs)
     *
     * @param mixed $data Metadata key/name or array of key value pairs
     * @param mixed $value Metadata value. Must be serializable if non-scalar, ignored if $key is array
     * @return mixed|false Meta ID on success for single, array of ids for array input, false on failure
     **/
    public function addUniqueMeta($data, $value = null)
    {
        return $this->addMeta($data, $value, true);
    }

    /**
     * Returns the value of the first PostMeta row matching the given key
     *
     * NOTE: This method assumes there is only one row for this post with the given meta_key
     * User getMeta for meta keys for which you expect to find multiple rows
     *
     * @param string $key The meta_key
     * @return string
     **/
    public function findMetaValue($key)
    {
        return $this->postMeta()
            ->where('meta_key', $key)
            ->first()
            ->meta_value;
    }

    /**
     * Returns the PostMeta rows matching the given key in a Collection
     *
     * @param string $key The meta_key
     * @return Illuminate\Support\Collection
     **/
    public function getMeta($key)
    {
        return $this->postMeta()
            ->where('meta_key', $key)
            ->get();
    }

    /**
     * A Post has many PostMeta
     **/
    public function postMeta()
    {
        return $this->hasMany(PostMeta::class, 'post_id', 'ID');
    }
}

