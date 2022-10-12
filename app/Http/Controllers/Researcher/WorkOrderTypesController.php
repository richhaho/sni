<?php

namespace App\Http\Controllers\Researcher;

use App\Http\Controllers\Controller;
use App\WorkOrderType;
use Illuminate\Http\Request;
use Session;

class WorkOrderTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = WorkOrderType::All();

        $data = [
            'types' => $types,
        ];

        return view('researcher.workordertypes.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name' => 'required',
        ]);

        $type = WorkOrderType::onlyTrashed()->where('slug', '=', str_slug($request->input('name')))->first();
        if ($type) {
            $type->restore();
        } else {
            $this->validate($request, [
                'name' => 'unique:work_order_types',
            ]);
            $type = new WorkOrderType();
            $type->slug = str_slug($request->input('name'));
            $type->name = $request->input('name');
            $type->save();
        }
        Session::flash('message', 'Work Order Type '.$type->name.' successfully created.');

        return redirect()->route('workordertypes.index');
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = WorkOrderType::where('slug', '=', $id)->firstOrFail();

        $temp_name = $type->name;
        $template = $type->template;
        if ($template) {
            foreach ($template->lines as $line) {
                $line->delete();
            }
            $template->delete();
        }
        $type->delete();

        Session::flash('message', 'Work Order Type '.$temp_name.' successfully deleted.');

        return redirect()->route('workordertypes.index');
    }
}
