<?php

namespace App\Http\Controllers\Researcher;

use App\Http\Controllers\Controller;
use App\PriceList;
use Illuminate\Http\Request;
use Session;

class PriceListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = PriceList::All();

        $data = [
            'list' => $list,

        ];

        return view('researcher.pricelist.index', $data);
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
            'new_description' => 'required',
            'new_price' => 'required|numeric',
        ]);

        $item = new PriceList();

        $item->description = $request->new_description;
        $item->price = $request->new_price;
        $item->save();

        Session::flash('message', 'Item '.$item->description.' successfully created.');

        return redirect()->route('pricelist.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    public function itemlist()
    {
        return PriceList::get()->ToJson();
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
        $this->validate($request, [
            'description' => 'required',
            'price' => 'required|numeric',
        ]);
        $item = PriceList::findOrFail($id);
        $item->update($request->all());

        Session::flash('message', 'Item '.$item->description.' successfully updated.');

        return redirect()->route('pricelist.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = PriceList::findOrFail($id);

        $item->delete();

        Session::flash('message', 'Item '.$item->description.' successfully deleted.');

        return redirect()->route('pricelist.index');
    }
}
