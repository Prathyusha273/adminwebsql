<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OnBoardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {

        return view("on-board.index");
    }


    public function show($id)
    {
        return view('on-board.save')->with('id', $id);
    }

    // DataTables provider (SQL)
    public function data(Request $request)
    {
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $draw = (int) $request->input('draw', 1);
        $search = strtolower((string) data_get($request->input('search'), 'value', ''));

        $q = DB::table('on_boarding');
        if ($search !== '') {
            $q->where(function($qq) use ($search){
                $qq->where('title','like','%'.$search.'%')
                   ->orWhere('description','like','%'.$search.'%')
                   ->orWhere('type','like','%'.$search.'%');
            });
        }
        $total = (clone $q)->count();
        $rows = $q->orderBy('title','asc')->offset($start)->limit($length)->get();

        $canDelete = in_array('onboard.edit', json_decode(@session('user_permissions'), true) ?: []); // delete permission key may differ; using edit group access
        $data = [];
        foreach ($rows as $r) {
            $editUrl = route('on-board.save',$r->id);
            $title = '<a href="'.$editUrl.'" class="redirecttopage">'.e($r->title ?: '').'</a>';
            $desc = e($r->description ?: '');
            $type = e($r->type ?: '');
            $actionsHtml = '<span class="action-btn"><a href="'.$editUrl.'"><i class="mdi mdi-lead-pencil" title="Edit"></i></a> <a href="javascript:void(0)" data-id="'.$r->id.'" class="delete-onboard"><i class="mdi mdi-delete" title="Delete"></i></a></span>';
            $data[] = [ $title, $desc, $type, $actionsHtml ];
        }
        return response()->json(['draw'=>$draw,'recordsTotal'=>$total,'recordsFiltered'=>$total,'data'=>$data]);
    }

    public function json($id)
    {
        $rec = DB::table('on_boarding')->where('id',$id)->first();
        if(!$rec) return response()->json(['error'=>'Not found'],404);
        return response()->json($rec);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'=>'required|string|max:255',
            'description'=>'required|string',
            'type'=>'nullable|string|max:255',
        ]);
        DB::table('on_boarding')->where('id',$id)->update([
            'title'=>$request->input('title'),
            'description'=>$request->input('description'),
            'type'=>$request->input('type'),
        ]);
        return response()->json(['success'=>true]);
    }

    public function destroy($id)
    {
        $exists = DB::table('on_boarding')->where('id',$id)->exists();
        if(!$exists) return response()->json(['success'=>false],404);
        DB::table('on_boarding')->where('id',$id)->delete();
        return response()->json(['success'=>true]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        if (empty($ids)) return response()->json(['success'=>false,'message'=>'No ids'],400);
        DB::table('on_boarding')->whereIn('id',$ids)->delete();
        return response()->json(['success'=>true]);
    }
}
