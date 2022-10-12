<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = ['workorder_id', 'invoice_id', 'invoice_line_id', 'template_id', 'name', 'decription', 'summary', 'instructions', 'todo_uploads', 'todo_instructions', 'status'];

    protected $table = 'todos';

    public function workorder()
    {
        return WorkOrder::where('id', $this->workorder_id)->first();
    }

    public function invoice()
    {
        return Invoice::where('id', $this->invoice_id)->first();
    }

    public function invoiceLine()
    {
        return InvoiceLine::where('id', $this->invoice_line_id)->first();
    }

    public function template()
    {
        return TemplateLine::where('id', $this->template_id)->first();
    }

    public function documents()
    {
        return TodoDocument::where('todo_id', $this->id)->get();
    }

    public function instructions()
    {
        return TodoInstruction::where('todo_id', $this->id)->get();
    }
}
