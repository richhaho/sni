<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TodoInstruction extends Model
{
    protected $fillable = ['todo_id', 'instruction'];

    protected $table = 'todo_instructions';

    public function todo()
    {
        return Todo::where('id', $this->todo_id)->first();
    }

    public function writer()
    {
        return User::where('id', $this->entered_by)->first();
    }
}
