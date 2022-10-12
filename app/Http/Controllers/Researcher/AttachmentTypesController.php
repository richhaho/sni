<?php

namespace App\Http\Controllers\Researcher;

use App\AttachmentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

class AttachmentTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = AttachmentType::orderBy('type', 'DESC')->get();

        $data = [
            'types' => $types,
        ];

        return view('researcher.attachmenttypes.index', $data);
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

        $type = AttachmentType::onlyTrashed()->where('slug', '=', str_slug($request->input('name')))->first();
        if ($type) {
            $type->restore();
        } else {
            $this->validate($request, [
                'name' => 'unique:attachment_types',
            ]);
            $type = new AttachmentType();
            $type->slug = str_slug($request->input('name'));
            $type->name = $request->input('name');
            $type->save();
        }
        Session::flash('message', 'Attacment Type '.$type->name.' successfully created.');

        return redirect()->route('attachmenttypes.index');
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
        $type = AttachmentType::where('slug', '=', $id)->firstOrFail();
        $temp_name = $type->name;
        $type->delete();

        Session::flash('message', 'Attachment Type '.$temp_name.' successfully deleted.');

        return redirect()->route('attachmenttypes.index');
    }
}
