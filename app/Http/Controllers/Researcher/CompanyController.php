<?php

namespace App\Http\Controllers\Researcher;

use App\CompanySetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = CompanySetting::first();
        if (! $company) {
            $company = new CompanySetting();
            $company->name = 'New Company';
            $company->save();
        }
        $data = [
            'company' => $company,
        ];

        return view('researcher.company.index', $data);
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
        //
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
        $company = CompanySetting::findOrFail($id);
        $company->update($request->all());
        if ($request->has('payeezy_mode')) {
            $company->payeezy_mode = 'live';
        } else {
            $company->payeezy_mode = 'sandbox';
        }
        $company->save();
        Session::flash('message', 'Successfully updated Company Info');

        return redirect()->route('company.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
