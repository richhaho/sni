<?php

namespace App\Http\Controllers\Admin;

use App\Client;
use App\Http\Controllers\Controller;
use App\Template;
use App\TemplateLine;
use App\WorkOrderType;
use Illuminate\Http\Request;
use Response;
use Session;

class TemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = Template::defaults()->paginate(15);
        $existent_types = Template::defaults()->pluck('type_slug')->toArray();
        $types = WorkOrderType::all()->pluck('name', 'slug')->toArray();
        foreach ($existent_types as $key) {
            unset($types[$key]);
        }

        $data = [
            'templates' => $templates,
            'types' => $types,
        ];

        return view('admin.templates.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->has('type')) {
            $type = $request->type;
        } else {
            $type = '';
        }

        $line_types = [
            'apply-always' => 'Apply Always',
            'apply-always-ss' => 'Apply Always (SS)',
            'aply-when-rush' => 'Apply when Rush',
            'apply-during-docgen' => 'Apply During DocGen',
            'apply-during-docgen-rush' => 'Apply During DocGen RUSH',
            'standard-mail' => 'Apply when Regular Mail',
            'certified-green' => 'Apply when Certfied Green RR',
            'certified-nongreen' => 'Apply when Certfied Non Green',
            'registered-mail' => 'Apply when Registered Mail',
            'express-mail' => 'Apply when Express Mail',
            'other-mail' => 'Apply when eMail',
            'standard-mail-ss' => 'Apply when Regular Mail (SS)',
            'certified-green-ss' => 'Apply when Certfied Green RR (SS)',
            'certified-nongreen-ss' => 'Apply when Certfied Non Green (SS)',
            'registered-mail-ss' => 'Apply when Registered Mail (SS)',
            'express-mail-ss' => 'Apply when Express Mail (SS)',
            'other-mail-ss' => 'Apply when eMail (SS)',
            'return-mail' => 'Apply when Return Recipient',
            'additional-service' => 'Additional Service',
        ];
        $existent_types = Template::defaults()->pluck('type_slug')->toArray();
        $types = WorkOrderType::all()->pluck('name', 'slug')->toArray();
        foreach ($existent_types as $key) {
            unset($types[$key]);
        }

        $data = [
            'types' => $types,
            'type' => $type,
            'line_types' => $line_types,
        ];

        return view('admin.templates.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'line_type' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'todo_name' => 'required_if:line_type,additional-service',
            'summary' => 'required_if:line_type,additional-service',
        ]);

        $template = new Template();
        $template->type_slug = $request->type;
        $template->enabled = 1;
        $template->save();

        $line = new TemplateLine();
        $line->type = $request->line_type;
        $line->description = $request->description;
        $line->quantity = $request->quantity;
        $line->price = $request->price;
        if ($request->line_type == 'additional-service') {
            $line->todo_name = $request->todo_name;
            $line->summary = $request->summary;
            $line->todo_instructions = $request->has('todo_instructions') ? 1 : 0;
            $line->todo_uploads = $request->has('todo_uploads') ? 1 : 0;
        }

        $template->lines()->save($line);

        Session::flash('message', 'New Template created');

        return redirect()->route('templates.edit', $template->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $template = Template::findOrFail($id);
        $line_types = [
            'apply-always' => 'Apply Always',
            'apply-always-ss' => 'Apply Always (SS)',
            'aply-when-rush' => 'Apply when Rush',
            'apply-during-docgen' => 'Apply During DocGen',
            'apply-during-docgen-rush' => 'Apply During DocGen RUSH',
            'standard-mail' => 'Apply when Regular Mail',
            'certified-green' => 'Apply when Certfied Green RR',
            'certified-nongreen' => 'Apply when Certfied Non Green',
            'registered-mail' => 'Apply when Registered Mail',
            'express-mail' => 'Apply when Express Mail',
            'other-mail' => 'Apply when eMail',
            'standard-mail-ss' => 'Apply when Regular Mail (SS)',
            'certified-green-ss' => 'Apply when Certfied Green RR (SS)',
            'certified-nongreen-ss' => 'Apply when Certfied Non Green (SS)',
            'registered-mail-ss' => 'Apply when Registered Mail (SS)',
            'express-mail-ss' => 'Apply when Express Mail (SS)',
            'other-mail-ss' => 'Apply when eMail (SS)',
            'return-mail' => 'Apply when Return Recipient',
            'additional-service' => 'Additional Service',
        ];
        //only types not assigned + current type
        $existent_types = Template::defaults()->pluck('type_slug')->toArray();
        $types = WorkOrderType::all()->pluck('name', 'slug')->toArray();
        foreach ($existent_types as $key) {
            unset($types[$key]);
        }
        $types[$template->type_slug] = $template->type->name;
        $data = [
            'types' => $types,
            'line_types' => $line_types,
            'template' => $template,
        ];

        return view('admin.templates.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'new_description.*' => 'required',
            'new_quantity.*' => 'required|numeric',
            'new_price.*' => 'required|numeric',
            'new_todo_name.*' => 'required_if:line_type,additional-service',
            'new_summary.*' => 'required_if:line_type,additional-service',
        ], [
            'new_description.*' => 'The Description is required',
            'new_quantity.*' => 'The quantity must be numeric',
            'new_price.*' => 'The price must be numeric',
            'new_todo_name.*' => 'To Do Name is required if line type is Additional Service.',
            'new_summary.*' => 'Summary is required if line type is Additional Service.',
        ]);

        //dd('valido');
        $template = Template::findOrFail($id);
        $template->type_slug = $request->type;
        $template->save();

        if ($request->input('line_type')) {
            foreach ($request->input('line_type') as $key => $linetype) {
                $line = TemplateLine::findOrFail($key);
                $line->type = $request->line_type[$key];
                $line->description = $request->description[$key];
                $line->quantity = $request->quantity[$key];
                $line->price = $request->price[$key];
                if ($request->line_type[$key] == 'additional-service') {
                    $line->todo_name = $request->todo_name[$key];
                    $line->summary = $request->summary[$key];
                    $line->todo_instructions = isset($request->todo_instructions[$key]) ? 1 : 0;
                    $line->todo_uploads = isset($request->todo_uploads[$key]) ? 1 : 0;
                }
                $line->save();
            }
        }

        if ($request->input('new_line_type')) {
            foreach ($request->input('new_line_type') as $key => $linetype) {
                $line = new TemplateLine();
                $line->type = $request->new_line_type[$key];
                $line->description = $request->new_description[$key];
                $line->quantity = $request->new_quantity[$key];
                $line->price = $request->new_price[$key];
                if ($request->new_line_type[$key] == 'additional-service') {
                    $line->todo_name = $request->new_todo_name[$key];
                    $line->summary = $request->new_summary[$key];
                    $line->todo_instructions = isset($request->new_todo_instructions[$key]) ? 1 : 0;
                    $line->todo_uploads = isset($request->new_todo_uploads[$key]) ? 1 : 0;
                }
                $template->lines()->save($line);
            }
        }

        return redirect()->route('templates.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $template = Template::findOrFail($id);
        $old_name = $template->type->name;
        $template->delete();

        Session::flash('message', 'Template deleted');

        return redirect()->route('templates.index');
    }

    /**
     * Download CSV
     *
     * @return Response
     */
    public function download(Request $request)
    {
        $result = Template::all();
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=templates.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        $columns = ['id', 'client', 'type', 'item_type', 'description', 'quantity', 'price', 'new_amount'];
        $callback = function () use ($result, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($result as $template) {
                foreach ($template->lines as $line) {
                    fputcsv($file, [
                        $line->id,
                        $template->clientName(),
                        $template->type_slug,
                        $line->type,
                        $line->description,
                        $line->quantity,
                        $line->price,
                        '',
                    ]);
                }
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Upload CSV
     *
     * @return Response
     */
    public function upload(Request $request)
    {
        $delimiter = ',';
        $f = $request->file('csv');
        if (! $f || $f == '') {
            Session::flash('message', 'Please input csv file.');

            return redirect()->route('templates.index');
        }
        $header = null;
        $data = [];
        if (($handle = fopen($f, 'r')) !== false) {
            try {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                    if (! $header) {
                        $header = $row;
                    } else {
                        $data[] = array_combine($header, $row);
                    }
                }
                fclose($handle);
            } catch (\Exception $e) {
                Session::flash('message', 'You uploaded invalid CSV file. Please upload valid one.');

                return redirect()->route('templates.index');
            }
        }
        $invalidError = $this->checkIfValidCSV($header, $data);
        if ($invalidError) {
            Session::flash('message', $invalidError);

            return redirect()->route('templates.index');
        }
        foreach ($data as $row) {
            $client_id = 0;
            if ($row['client'] != 'Default') {
                $client = Client::where('company_name', $row['client'])->first();
                if (! $client) {
                    continue;
                }
                $client_id = $client->id;
            }

            $template = Template::where('client_id', $client_id)->where('type_slug', $row['type'])->first();
            if (! $template) {
                continue;
            }

            $line = $template->lines()->where('id', $row['id'])->first();
            if (! $line) {
                continue;
            }
            if (! $row['new_amount']) {
                continue;
            }
            $line->price = $row['new_amount'];
            $line->save();
        }

        Session::flash('message', 'Templates Updated from uploaded csv file.');

        return redirect()->route('templates.index');
    }

    public function checkIfValidCSV($header, $data)
    {
        $columns = ['id', 'client', 'type', 'item_type', 'description', 'quantity', 'price', 'new_amount'];
        if ($columns != $header) {
            return 'Error: CSV header does not match. Header should be description and price.';
        }
        foreach ($data as $row) {
            if (! is_numeric($row['price'])) {
                return 'Error: price must be numeric value on your csv file.';
            }
            if ($row['new_amount']) {
                if (! is_numeric($row['new_amount'])) {
                    return 'Error: new_amount must be numeric value on your csv file.';
                }
            }
            if (! is_numeric($row['quantity'])) {
                return 'Error: quantity must be numeric value on your csv file.';
            }

            if ((int) $row['quantity'] != $row['quantity']) {
                return 'Error: quantity must be integer value on your csv file.';
            }
        }

        return null;
    }
}
