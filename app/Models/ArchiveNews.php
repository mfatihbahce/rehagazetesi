<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveNews extends Model
{
    protected $connection = 'archive';

    public $timestamps = false;

    public function getTable()
    {
        return config('archive.tables.news', 'news');
    }

    public function getKeyName()
    {
        return config('archive.columns.news.primary_key', 'id');
    }
}
