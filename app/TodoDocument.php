<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TodoDocument extends Model
{
    protected $table='todo_documents';

    public function todo()
    {
        return Todo::where('id', $this->todo_id)->first();
    }
}
