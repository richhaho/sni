<?php

namespace App\Http\Controllers\Admin;

use App\Report;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Response;
use Session;
use DB;

class ReportsController extends Controller
{
     
    public function __construct() {
     
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
       $reports =Report::query();
       $reports= $reports->orderBy('id', 'desc')->paginate(15);
       $data = [
           'reports' => $reports,
       ];
       return view('admin.reports.index',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data=$request->all();
        $data['created_at'] = date('Y-m-d H:i:s');
        $report =  Report::create($data);
        Session::flash('message', 'New report created.');
        return redirect()->back();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $report =Report::where('id', $id)->first();
        $data = $request->all();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $report->update($data);

        Session::flash('message', $report->name." was Updated.");
        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function run(Request $request, $id)
    {
        $report =Report::where('id', $id)->first();
        $client_id = $request->client_id;
        $sql = str_replace("@client", "$client_id", $report->sql);
        try {
            $result = DB::select($sql);
        } catch (\Exception $e) {
            Session::flash('message', "Error: ". $e->getMessage());
            return redirect()->back();
        }
        if (count($result)==0) {
            Session::flash('message', "The report's query does not return any data.");
            return redirect()->back();
        }

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=report.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $columns = [];
        foreach ($result[0] as $key => $val) {
            $columns[] = $key;
        }
        $callback = function() use ($result, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($result as $row) {
                fputcsv($file, (array) $row);
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public function destroy($id)
    {
        $report =Report::where('id', $id)->first();
        $report->delete();

        Session::flash('message', $report->name." was deleted.");
        return redirect()->back();
    }
}
