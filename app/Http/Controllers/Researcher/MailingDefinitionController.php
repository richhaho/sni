<?php

namespace App\Http\Controllers\Researcher;

use App\Http\Controllers\Controller;
use App\MailingType;
use Illuminate\Http\Request;
use Session;

class MailingDefinitionController extends Controller
{
    public function index()
    {
        $definitions = MailingType::all();

        $mailing_types = [
            'standard-mail' => 'Regular Mail',
            'certified-green' => 'Certfied Green RR',
            'certified-nongreen' => 'Certfied Non Green',
            'registered-mail' => 'Registered Mail',
            'express-mail' => 'Express Mail',
            'other-mail' => 'eMail',
        ];

        if (! count($definitions) > 0) {
            foreach ($mailing_types as $key => $val) {
                MailingType::create(['type' => $key]);
            }
            $definitions = MailingType::all();
        }

        $data = [
            'definitions' => $definitions,
            'mailing_types' => $mailing_types,
        ];

        return view('researcher.mailingtype.index', $data);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'type.*' => 'required',
            'postage.*' => 'required|numeric',
            'fee.*' => 'required|numeric',
        ]);

        foreach ($request->type as $type) {
            $mt = MailingType::where('type', $type)->first();
            $mt->update([
                'postage' => $request->postage[$type],
                'fee' => $request->fee[$type],
                'stc' => $request->stc[$type],
            ]);
        }

        Session::flash('message', 'Mailing Type definitons Updated');

        return redirect()->route('mailingtype.index');
    }
}
