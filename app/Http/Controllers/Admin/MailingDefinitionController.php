<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MailingType;
use Response;
use Session;

class MailingDefinitionController extends Controller
{
    public function index() {
        $definitions = MailingType::all();
        
        $mailing_types = [
            'standard-mail' => 'Regular Mail',
            'certified-green' => 'Certfied Green RR',   
            'certified-nongreen' => 'Certfied Non Green', 
            'registered-mail' => 'Registered Mail',
            'express-mail' => 'Express Mail',
            'other-mail' => 'eMail',
        ];

        if(!count($definitions) >0 ) {
            foreach( $mailing_types as $key => $val) {
                MailingType::create(['type'=> $key]);
            }
            $definitions = MailingType::all();
        }

        

        $data = [
            'definitions' => $definitions,
            'mailing_types' => $mailing_types,
        ];
        
        return view('admin.mailingtype.index',$data);
    }

    public function update(Request $request) {
        
        $this->validate($request,[
            'type.*' => 'required',
            'postage.*' => 'required|numeric',
            'fee.*' => 'required|numeric' 
        ]);

        foreach($request->type as $type) {
            $mt = MailingType::where('type',$type)->first();
            $mt->update([
                'postage' => $request->postage[$type],
                'fee' =>$request->fee[$type],
                'stc' => $request->stc[$type]
            ]);
        }    


        Session::flash('message','Mailing Type definitons Updated');
        return redirect()->route('mailingtype.index');
       

    }

    /**
     * Download CSV
     *
     * @return Response
     */
    public function download(Request $request)
    {
        $result = MailingType::all();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=mailing-type.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $columns = ['type', 'postage', 'fee', 'stc'];
        $callback = function() use ($result, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($result as $row) {
                fputcsv($file, [$row->type, $row->postage, $row->fee, $row->stc]);
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
            return redirect()->route('mailingtype.index');
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
                return redirect()->route('mailingtype.index'); 
            }
        }
        $invalidError = $this->checkIfValidCSV($header, $data);
        if ($invalidError) {
            Session::flash('message', $invalidError);
            return redirect()->route('mailingtype.index');    
        }
        foreach($data as $row) {
            $mt = MailingType::where('type',$row['type'])->first();
            if (empty($mt)) {
                $mt = MailingType::create($row);
            } else {
                $mt->update($row);
            }
        }

        Session::flash('message','Mailing Type definitons Updated from uploaded csv file.');
        return redirect()->route('mailingtype.index');
    }

    public function checkIfValidCSV($header, $data) {
        $columns = ['type', 'postage', 'fee', 'stc'];
        if ($columns != $header) return 'Error: CSV header does not match. Header should be type, postage, fee and stc.';
        foreach($data as $row) {
            if (!is_numeric($row['postage']) || !is_numeric($row['fee'])) {
                return 'Error: postage and fee must be numeric value on your csv file.';
            }
        }
        return null;
    }
}
