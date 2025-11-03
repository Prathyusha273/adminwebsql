<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Media as MediaModel;

class MediaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){
        return view('media.index');
    }

    public function edit($id)
    {
    	return view('media.edit')->with('id', $id);
    }

    public function create()
    {
        return view('media.create');
    }

    // DataTables provider
    public function data(Request $request)
    {
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $draw = (int) $request->input('draw', 1);
        $search = strtolower((string) data_get($request->input('search'), 'value', ''));

        $q = DB::table('media');
        if ($search !== '') {
            $q->where(function($qq) use ($search){
                $qq->where('name','like','%'.$search.'%')
                   ->orWhere('slug','like','%'.$search.'%');
            });
        }
        $total = (clone $q)->count();
        $rows = $q->orderBy('name','asc')->offset($start)->limit($length)->get();

        $data = [];
        foreach ($rows as $r) {
            $editUrl = route('media.edit', $r->id);
            $checkbox = '<input type="checkbox" id="is_open_'.$r->id.'" name="record" class="is_open" dataId="'.$r->id.'"><label class="col-3 control-label" for="is_open_'.$r->id.'"></label>';
            $img = $r->image_path ? '<img src="'.e($r->image_path).'" style="width:70px;height:70px;border-radius:5px;" onerror="this.onerror=null;this.src=\'' . asset('images/placeholder.png') . '\'">' : '';
            $info = $img . '<a href="'.$editUrl.'">'.e($r->name ?: 'UNKNOWN').'</a>';
            $slug = e($r->slug ?: '');
            $actions = '<span class="action-btn"><a href="'.$editUrl.'" class="link-td"><i class="mdi mdi-lead-pencil" title="Edit"></i></a> '
                     . '<a href="javascript:void(0)" class="delete-btn" data-id="'.$r->id.'"><i class="mdi mdi-delete" title="Delete"></i></a></span>';
            $data[] = [ $checkbox, $info, $slug, $actions ];
        }
        return response()->json(['draw'=>$draw,'recordsTotal'=>$total,'recordsFiltered'=>$total,'data'=>$data]);
    }

    public function json($id)
    {
        $rec = DB::table('media')->where('id',$id)->first();
        if(!$rec) return response()->json(['error'=>'Not found'],404);
        return response()->json($rec);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'image' => 'required|file|image|max:5120',
        ]);
        $id = (string) Str::uuid();
        $now = Carbon::now()->toIso8601String();
        $slug = $request->input('slug') ?: Str::slug('media-'.$request->input('name'));

        $path = $request->file('image')->store('public/media');
        $url = Storage::url($path);
        $imageName = basename($path);

        MediaModel::create([
            'id' => $id,
            'name' => $request->input('name'),
            'slug' => $slug,
            'image_name' => $imageName,
            'image_path' => $url,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return response()->json(['success'=>true,'id'=>$id]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'image' => 'nullable|file|image|max:5120',
        ]);
        $rec = MediaModel::findOrFail($id);
        $slug = $request->input('slug') ?: Str::slug('media-'.$request->input('name'));
        $data = [
            'name' => $request->input('name'),
            'slug' => $slug,
            'updated_at' => Carbon::now()->toIso8601String(),
        ];
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/media');
            $url = Storage::url($path);
            $imageName = basename($path);
            $data['image_name'] = $imageName;
            $data['image_path'] = $url;
        }
        $rec->update($data);
        return response()->json(['success'=>true]);
    }

    public function destroy($id)
    {
        $rec = MediaModel::find($id);
        if(!$rec) return response()->json(['success'=>false],404);
        $rec->delete();
        return response()->json(['success'=>true]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        if (empty($ids)) return response()->json(['success'=>false,'message'=>'No ids'],400);
        MediaModel::whereIn('id',$ids)->delete();
        return response()->json(['success'=>true]);
    }
}
