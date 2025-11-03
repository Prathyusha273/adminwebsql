<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\MenuItem;

class MenuItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('settings.menu_items.index');
    }

    public function create()
    {
        return view('settings.menu_items.create');
    }

    public function edit($id)
    {
        return view('settings.menu_items.edit')->with('id', $id);
    }


    // DataTables provider (SQL)
    public function data(Request $request)
    {
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = strtolower((string) data_get($request->input('search'), 'value', ''));
        $zoneFilter = (string) $request->input('zoneId', '');

        $q = DB::table('menu_items');
        if ($zoneFilter !== '') {
            $q->where('zoneId', $zoneFilter);
        }
        if ($search !== '') {
            $q->where(function($qq) use ($search){
                $qq->where('title','like','%'.$search.'%')
                   ->orWhere('position','like','%'.$search.'%')
                   ->orWhere('zoneTitle','like','%'.$search.'%');
            });
        }
        $total = (clone $q)->count();
        $rows = $q->orderBy('title','asc')->offset($start)->limit($length)->get();

        $items = [];
        foreach ($rows as $r) {
            $items[] = [
                'id' => $r->id,
                'title' => (string) ($r->title ?? ''),
                'position' => (string) ($r->position ?? ''),
                'zoneTitle' => (string) ($r->zoneTitle ?? ''),
                'is_publish' => (bool) ($r->is_publish ?? 0),
                'photo' => (string) ($r->photo ?? ''),
            ];
        }
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $items,
        ]);
    }

    public function json($id)
    {
        $doc = MenuItem::find($id);
        if (!$doc) {
            return response()->json(['error' => 'Not found'], 404);
        }
        return response()->json($doc);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'set_order' => 'nullable|integer',
            'position' => 'nullable|string|max:255',
            'photo' => 'nullable|image',
            'redirect_type' => 'nullable|string|max:255',
            'redirect_id' => 'nullable|string|max:2000',
            'zoneId' => 'nullable|string|max:255',
            'zoneTitle' => 'nullable|string|max:255',
        ]);
        $id = (string) Str::uuid();
        $imageUrl = null;
        if ($request->hasFile('photo')) {
            $imageUrl = Storage::url($request->file('photo')->store('public/uploads/menu-items'));
        }
        MenuItem::create([
            'id' => $id,
            'title' => $request->input('title',''),
            'set_order' => (int) $request->input('set_order',0),
            'position' => $request->input('position','top'),
            'photo' => $imageUrl,
            'redirect_type' => $request->input('redirect_type','external_link'),
            'redirect_id' => $request->input('redirect_id'),
            'is_publish' => $request->boolean('is_publish') ? 1 : 0,
            'zoneId' => $request->input('zoneId'),
            'zoneTitle' => $request->input('zoneTitle'),
        ]);
        return response()->json(['success'=>true,'id'=>$id]);
    }

    public function update(Request $request, $id)
    {
        $mi = MenuItem::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'set_order' => 'nullable|integer',
            'position' => 'nullable|string|max:255',
            'photo' => 'nullable|image',
            'redirect_type' => 'nullable|string|max:255',
            'redirect_id' => 'nullable|string|max:2000',
            'zoneId' => 'nullable|string|max:255',
            'zoneTitle' => 'nullable|string|max:255',
        ]);
        $imageUrl = $mi->photo;
        if ($request->hasFile('photo')) {
            $imageUrl = Storage::url($request->file('photo')->store('public/uploads/menu-items'));
        }
        $mi->update([
            'title' => $request->input('title',''),
            'set_order' => (int) $request->input('set_order',0),
            'position' => $request->input('position','top'),
            'photo' => $imageUrl,
            'redirect_type' => $request->input('redirect_type','external_link'),
            'redirect_id' => $request->input('redirect_id'),
            'is_publish' => $request->boolean('is_publish') ? 1 : 0,
            'zoneId' => $request->input('zoneId'),
            'zoneTitle' => $request->input('zoneTitle'),
        ]);
        return response()->json(['success'=>true]);
    }

    public function togglePublish($id)
    {
        $doc = MenuItem::find($id);
        if (!$doc) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        $doc->is_publish = $doc->is_publish ? 0 : 1;
        $doc->save();
        return response()->json(['success' => true, 'is_publish' => (bool) $doc->is_publish]);
    }

    public function destroy($id)
    {
        $doc = MenuItem::find($id);
        if (!$doc) {
            return response()->json(['success' => false], 404);
        }
        $doc->delete();
        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No ids'], 400);
        }
        MenuItem::whereIn('id', $ids)->delete();
        return response()->json(['success' => true]);
    }
}


