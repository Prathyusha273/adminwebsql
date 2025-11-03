<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\CmsPage;

class CmsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
         return view('cms.index');
    }
    public function edit($id)
    {
        return view('cms.edit')->with('id',$id);
    }

    public function create()
    {
        return view('cms.create');
    }

    // DataTables provider
    public function data(Request $request)
    {
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $draw = (int) $request->input('draw', 1);
        $search = strtolower((string) data_get($request->input('search'), 'value', ''));

        $q = DB::table('cms_pages');
        if ($search !== '') {
            $q->where(function($qq) use ($search){
                $qq->where('name','like','%'.$search.'%')
                   ->orWhere('slug','like','%'.$search.'%');
            });
        }
        $total = (clone $q)->count();
        $rows = $q->orderBy('name','asc')->offset($start)->limit($length)->get();

        $canDelete = in_array('cms.delete', json_decode(@session('user_permissions'), true) ?: []);
        $data = [];
        foreach ($rows as $r) {
            $editUrl = route('cms.edit',$r->id);
            $name = '<a href="'.$editUrl.'" class="redirecttopage">'.e($r->name ?: '').'</a>';
            $slug = e($r->slug ?: '');
            $toggle = $r->publish ? '<label class="switch"><input type="checkbox" checked data-id="'.$r->id.'" class="toggle-publish"><span class="slider round"></span></label>'
                                  : '<label class="switch"><input type="checkbox" data-id="'.$r->id.'" class="toggle-publish"><span class="slider round"></span></label>';
            $actionsHtml = '<span class="action-btn"><a href="'.$editUrl.'"><i class="mdi mdi-lead-pencil" title="Edit"></i></a>';
            if ($canDelete) {
                $actionsHtml .= ' <a href="javascript:void(0)" data-id="'.$r->id.'" class="delete-cms"><i class="mdi mdi-delete" title="Delete"></i></a>';
            }
            $actionsHtml .= '</span>';
            if ($canDelete) {
                $select = '<td class="delete-all"><input type="checkbox" class="is_open" dataId="'.$r->id.'"><label class="col-3 control-label"></label></td>';
                $data[] = [ $select, $name, $slug, $toggle, $actionsHtml ];
            } else {
                $data[] = [ $name, $slug, $toggle, $actionsHtml ];
            }
        }
        return response()->json(['draw'=>$draw,'recordsTotal'=>$total,'recordsFiltered'=>$total,'data'=>$data]);
    }

    public function json($id)
    {
        $p = CmsPage::find($id);
        if(!$p) return response()->json(['error'=>'Not found'],404);
        return response()->json($p);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'slug'=>'required|string|max:255|unique:cms_pages,slug',
            'description'=>'nullable|string',
        ]);
        $id = (string) Str::uuid();
        CmsPage::create([
            'id'=>$id,
            'name'=>$request->input('name'),
            'slug'=>$request->input('slug'),
            'description'=>$request->input('description'),
            'publish'=>$request->boolean('publish')?1:0,
        ]);
        return response()->json(['success'=>true,'id'=>$id]);
    }

    public function update(Request $request, $id)
    {
        $p = CmsPage::findOrFail($id);
        $request->validate([
            'name'=>'required|string|max:255',
            'slug'=>'required|string|max:255|unique:cms_pages,slug,'.$p->id.',id',
            'description'=>'nullable|string',
        ]);
        $p->update([
            'name'=>$request->input('name'),
            'slug'=>$request->input('slug'),
            'description'=>$request->input('description'),
            'publish'=>$request->boolean('publish')?1:0,
        ]);
        return response()->json(['success'=>true]);
    }

    public function toggle($id, Request $request)
    {
        $p = CmsPage::findOrFail($id);
        $p->publish = $request->boolean('publish') ? 1 : 0;
        $p->save();
        return response()->json(['success'=>true,'id'=>$p->id,'publish'=>(bool)$p->publish]);
    }

    public function destroy($id)
    {
        $p = CmsPage::find($id);
        if(!$p) return response()->json(['success'=>false],404);
        $p->delete();
        return response()->json(['success'=>true]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        if (empty($ids)) return response()->json(['success'=>false,'message'=>'No ids'],400);
        CmsPage::whereIn('id',$ids)->delete();
        return response()->json(['success'=>true]);
    }
}
