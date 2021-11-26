<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;
    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * 子フォルダ
     *
     * @return void
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
