<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\MartBanner;

class MartBannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Views
    public function index()
    {
        return view('martBanners.index');
    }

    public function create()
    {
        return view('martBanners.create');
    }

    public function edit($id)
    {
        return view('martBanners.edit')->with('id', $id);
    }

    // DataTables provider (SQL)
    public function data(Request $request)
    {
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = strtolower((string) data_get($request->input('search'), 'value', ''));

        $q = DB::table('mart_banners');
        if ($search !== '') {
            $q->where(function($qq) use ($search){
                $qq->where('title','like','%'.$search.'%')
                   ->orWhere('position','like','%'.$search.'%')
                   ->orWhere('zoneTitle','like','%'.$search.'%');
            });
        }
        $total = (clone $q)->count();
        $rows = $q->orderBy('set_order','asc')->offset($start)->limit($length)->get();

        $items = [];
        foreach ($rows as $r) {
            $items[] = [
                'id' => $r->id,
                'title' => (string) ($r->title ?? ''),
                'position' => (string) ($r->position ?? ''),
                'zoneTitle' => (string) ($r->zoneTitle ?? ''),
                'set_order' => (int) ($r->set_order ?? 0),
                'is_publish' => (bool) ($r->is_publish ?? 0),
                'photo' => (string) ($r->photo ?? ''),
            ];
        }
        return response()->json(['draw'=>$draw,'recordsTotal'=>$total,'recordsFiltered'=>$total,'data'=>$items]);
    }

    // JSON for single banner
    public function json($id)
    {
        $b = MartBanner::find($id);
        if(!$b) return response()->json(['error'=>'Not found'],404);
        return response()->json($b);
    }

    // Create (SQL)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'set_order' => 'nullable|integer',
            'position' => 'nullable|string|max:255',
            'screen' => 'nullable|string|max:255',
            'photo' => 'nullable|image',
            'redirect_type' => 'nullable|string|max:255',
            'external_link' => 'nullable|string|max:2000',
            'ads_link' => 'nullable|string|max:2000',
        ]);
        $id = (string) Str::uuid();
        $imageUrl = null;
        if ($request->hasFile('photo')) {
            $imageUrl = Storage::url($request->file('photo')->store('public/uploads/mart-banners'));
        }
        MartBanner::create([
            'id' => $id,
            'title' => $request->input('title',''),
            'description' => $request->input('description',''),
            'text' => $request->input('text',''),
            'photo' => $imageUrl,
            'position' => $request->input('position','top'),
            'screen' => $request->input('screen','home'),
            'redirect_type' => $request->input('redirect_type','external_link'),
            'storeId' => $request->input('storeId'),
            'productId' => $request->input('productId'),
            'martCategoryId' => $request->input('martCategoryId'),
            'ads_link' => $request->input('ads_link'),
            'external_link' => $request->input('external_link'),
            'is_publish' => $request->boolean('is_publish') ? 1 : 0,
            'set_order' => (int) $request->input('set_order', 0),
            'zoneId' => $request->input('zoneId') ?? $request->input('zone_select'),
            'zoneTitle' => $request->input('zoneTitle') ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return response()->json(['success'=>true,'id'=>$id]);
    }

    // Update (SQL)
    public function update(Request $request, $id)
    {
        $b = MartBanner::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'set_order' => 'nullable|integer',
            'position' => 'nullable|string|max:255',
            'screen' => 'nullable|string|max:255',
            'photo' => 'nullable|image',
            'redirect_type' => 'nullable|string|max:255',
            'external_link' => 'nullable|string|max:2000',
            'ads_link' => 'nullable|string|max:2000',
        ]);
        $imageUrl = $b->photo;
        if ($request->hasFile('photo')) {
            $imageUrl = Storage::url($request->file('photo')->store('public/uploads/mart-banners'));
        }
        $b->update([
            'title' => $request->input('title',''),
            'description' => $request->input('description',''),
            'text' => $request->input('text',''),
            'photo' => $imageUrl,
            'position' => $request->input('position','top'),
            'screen' => $request->input('screen','home'),
            'redirect_type' => $request->input('redirect_type','external_link'),
            'storeId' => $request->input('storeId'),
            'productId' => $request->input('productId'),
            'martCategoryId' => $request->input('martCategoryId'),
            'ads_link' => $request->input('ads_link'),
            'external_link' => $request->input('external_link'),
            'is_publish' => $request->boolean('is_publish') ? 1 : 0,
            'set_order' => (int) $request->input('set_order', 0),
            'zoneId' => $request->input('zoneId') ?? $request->input('zone_select'),
            'zoneTitle' => $request->input('zoneTitle') ?? '',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return response()->json(['success'=>true]);
    }

    // Toggle publish
    public function togglePublish($id)
    {
        $b = MartBanner::find($id);
        if(!$b) return response()->json(['success'=>false,'message'=>'Not found'],404);
        $b->is_publish = $b->is_publish ? 0 : 1;
        $b->updated_at = date('Y-m-d H:i:s');
        $b->save();
        return response()->json(['success'=>true,'is_publish'=>(bool)$b->is_publish]);
    }

    // Delete
    public function destroy($id)
    {
        $b = MartBanner::find($id);
        if(!$b) return response()->json(['success'=>false],404);
        $b->delete();
        return response()->json(['success'=>true]);
    }

    // Bulk delete
    public function bulkDelete(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success'=>false,'message'=>'No ids'],400);
        }
        MartBanner::whereIn('id',$ids)->delete();
        return response()->json(['success'=>true]);
    }
}
