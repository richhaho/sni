<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PriceList;
use Session;
use Response;

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
                
        return view('admin.pricelist.index',$data);
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
        
        Session::flash('message', 'Item ' . $item->description . ' successfully created.');
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

    public function itemlist(){
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
        $item->update($request->all());;
        
        Session::flash('message', 'Item ' . $item->description . ' successfully updated.');
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
        
         Session::flash('message', 'Item ' .$item->description . ' successfully deleted.');
        
        return redirect()->route('pricelist.index');
    }

    /**
     * Download CSV
     *
     * @return Response
     */
    public function download(Request $request)
    {
        $result = PriceList::all();
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=price_list.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $columns = ['description', 'price'];
        $callback = function() use ($result, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($result as $row) {
                fputcsv($file, [$row->description, $row->price]);
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
        $f= $request->file('csv');
        if (!$f || $f=='') {
            Session::flash('message','Please input csv file.');
            return redirect()->route('pricelist.index');
        }
        $header = null;
        $data = array();
        if (($handle = fopen($f, 'r')) !== false) {
            try {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
                {
                    if (!$header)
                        $header = $row;
                    else
                        $data[] = array_combine($header, $row);
                }
                fclose($handle);
            } catch (\Exception $e) {
                Session::flash('message','You uploaded invalid CSV file. Please upload valid one.');
                return redirect()->route('pricelist.index'); 
            }
        }
        $invalidError = $this->checkIfValidCSV($header, $data);
        if ($invalidError) {
            Session::flash('message', $invalidError);
            return redirect()->route('pricelist.index');    
        }
        foreach($data as $row) {
            $mt = PriceList::where('description',$row['description'])->first();
            if (empty($mt)) {
                $mt = PriceList::create($row);
            } else {
                $mt->update($row);
            }
        }

        Session::flash('message','Price list Updated from uploaded csv file.');
        return redirect()->route('pricelist.index');
    }

    public function checkIfValidCSV($header, $data) {
        $columns = ['description', 'price'];
        if ($columns != $header) return 'Error: CSV header does not match. Header should be description and price.';
        foreach($data as $row) {
            if (!is_numeric($row['price'])) {
                return 'Error: price must be numeric value on your csv file.';
            }
        }
        return null;
    }
}
