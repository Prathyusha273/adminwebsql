<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function social()
    {
        return view("settings.app.social");
    }

    public function globals()
    {
        return view("settings.app.global");
    }

    public function notifications()
    {
        return view("settings.app.notification");
    }

    public function cod()
    {
        return view('settings.app.cod');
    }

    public function applePay()
    {
        return view('settings.app.applepay');
    }

    public function stripe()
    {
        return view('settings.app.stripe');
    }

    public function mobileGlobals()
    {
        return view('settings.mobile.globals');
    }

    public function razorpay()
    {
        return view('settings.app.razorpay');
    }

    public function paytm()
    {
        return view('settings.app.paytm');
    }

    public function payfast()
    {
        return view('settings.app.payfast');
    }

    public function paypal()
    {
        return view('settings.app.paypal');
    }

    public function orangepay()
    {
        return view('settings.app.orangepay');
    }

    public function xendit()
    {
        return view('settings.app.xendit');
    }

    public function midtrans()
    {
        return view('settings.app.midtrans');
    }

    public function adminCommission()
    {
        return view("settings.app.adminCommission");
    }

    public function radiosConfiguration()
    {
        return view("settings.app.radiosConfiguration");
    }

    public function wallet()
    {
        return view('settings.app.wallet');
    }

    public function bookTable()
    {
        return view('settings.app.bookTable');
    }


    public function paystack()
    {
        return view('settings.app.paystack');
    }

    public function flutterwave()
    {
        return view('settings.app.flutterwave');
    }

    public function mercadopago()
    {
        return view('settings.app.mercadopago');
    }

    public function deliveryCharge()
    {
        return view("settings.app.deliveryCharge");
    }
    public function martSettings()
    {
        return view("settings.app.martSettings");
    }
    public function appSettings()
    {
        return view("settings.app.appSettings");
    }
    public function priceSetting()
    {
        return view("settings.app.priceSettings");
    }
    public function languages()
    {
        return view('settings.languages.index');
    }

    public function languagesedit($id)
    {
        return view('settings.languages.edit')->with('id', $id);
    }

    public function languagescreate()
    {
        return view('settings.languages.create');
    }

    public function specialOffer()
    {
        return view('settings.app.specialDiscountOffer');
    }

    public function story()
    {
        return view('settings.app.story');

    }

    public function footerTemplate()
    {
        return view('footerTemplate.index');
    }

    public function homepageTemplate()
    {
        return view('homepage_Template.index');
    }

    public function emailTemplatesIndex()
    {
        return view('email_templates.index');
    }

    public function emailTemplatesSave($id = '')
    {

        return view('email_templates.save')->with('id', $id);
    }
    public function documentVerification()
    {
        return view('settings.app.documentVerificationSetting');
    }

    public function surgeRules()
    {
      return view('settings.app.surgeRules');
    }

    // Email templates SQL endpoints
    public function emailTemplatesData()
    {
        $start = (int) request('start', 0);
        $length = (int) request('length', 10);
        $draw = (int) request('draw', 1);
        $search = strtolower((string) data_get(request('search'), 'value', ''));

        $q = DB::table('email_templates');
        if ($search !== '') {
            $q->where(function($qq) use ($search){
                $qq->where('type','like','%'.$search.'%')
                   ->orWhere('subject','like','%'.$search.'%');
            });
        }
        $total = (clone $q)->count();
        $rows = $q->orderBy('type','asc')->offset($start)->limit($length)->get();

        $data = [];
        foreach ($rows as $r) {
            $editUrl = route('email-templates.save', $r->id);
            $typeLabel = $r->type; // mapping done in view if needed
            $actions = '<span class="action-btn"><a href="'.$editUrl.'"><i class="mdi mdi-lead-pencil" title="Edit"></i></a> '
                    .'<a href="javascript:void(0)" class="delete-template" data-id="'.$r->id.'"><i class="mdi mdi-delete" title="Delete"></i></a></span>';
            $data[] = [ $typeLabel, e($r->subject ?: ''), $actions ];
        }
        return response()->json(['draw'=>$draw,'recordsTotal'=>$total,'recordsFiltered'=>$total,'data'=>$data]);
    }

    public function emailTemplatesJson($id)
    {
        $rec = DB::table('email_templates')->where('id',$id)->first();
        if(!$rec) return response()->json(['error'=>'Not found'],404);
        return response()->json($rec);
    }

    public function emailTemplatesUpdate($id)
    {
        request()->validate([
            'subject'=>'required|string',
            'message'=>'required|string',
            'isSendToAdmin'=>'nullable'
        ]);
        $updated = DB::table('email_templates')->where('id',$id)->update([
            'subject'=>request('subject'),
            'message'=>request('message'),
            'isSendToAdmin'=>request()->boolean('isSendToAdmin') ? 1 : 0,
        ]);
        if ($updated === false) return response()->json(['success'=>false],500);
        return response()->json(['success'=>true]);
    }

    public function emailTemplatesDelete($id)
    {
        $exists = DB::table('email_templates')->where('id',$id)->exists();
        if(!$exists) return response()->json(['success'=>false],404);
        DB::table('email_templates')->where('id',$id)->delete();
        return response()->json(['success'=>true]);
    }
}
