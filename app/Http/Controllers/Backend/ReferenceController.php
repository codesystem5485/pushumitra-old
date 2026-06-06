<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ReferenceModel;
use Auth;
use DB;
use Illuminate\Http\Request;
use Session;

class ReferenceController extends Controller
{
    protected $url = '';

    public function __construct()
    {
        $this->url = [
            'listUrl' => route('references.index'),
            'createUrl' => route('references.create'),
        ];
    }

    public function index()
    {
        $references = ReferenceModel::orderBy('id', 'DESC')->get();
        return view('backend.references.index', ['references' => $references, 'url' => $this->url]);
    }

    public function create()
    {
        return view('backend.references.create', ['url' => $this->url]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'institution_code' => 'nullable|max:255',
        ]);

        DB::beginTransaction();
        try {
            $reference = ReferenceModel::create([
                'name' => $request->input('name'),
                'collaboration_code' => $this->generateCollaborationCode(),
                'institution_code' => $request->input('institution_code'),
                'active' => $request->has('active') ? 1 : 0,
                'create_by' => Auth::id(),
            ]);

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            storeActicityLog(trans('messages.create'), 'Reference created: '.$reference->name, Auth::user(), $reference);
            return redirect()->route('references.index');
        } catch (\Exception $e) {
            DB::rollback();
            storeActicityLog(trans('messages.error'), $e->getMessage(), Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('references.index');
        }
    }

    public function edit(Request $request, $id = '')
    {
        $reference = ReferenceModel::findOrFail($id);
        return view('backend.references.create', ['reference' => $reference, 'url' => $this->url]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'institution_code' => 'nullable|max:255',
        ]);

        DB::beginTransaction();
        try {
            $reference = ReferenceModel::findOrFail($id);
            $reference->name = $request->input('name');
            $reference->institution_code = $request->input('institution_code');
            $reference->active = $request->has('active') ? 1 : 0;
            $reference->updated_by = Auth::id();
            $reference->save();

            DB::commit();
            Session::flash('success', trans('messages.update_records'));
            storeActicityLog(trans('messages.update'), 'Reference updated: '.$reference->name, Auth::user(), $reference);
            return redirect()->route('references.index');
        } catch (\Exception $e) {
            DB::rollback();
            storeActicityLog(trans('messages.error'), $e->getMessage(), Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('references.index');
        }
    }

    public function delete($id)
    {
        $reference = ReferenceModel::findOrFail($id);
        $reference->active = 0;
        $reference->updated_by = Auth::id();
        $reference->save();

        Session::flash('success', trans('messages.delete_records'));
        storeActicityLog(trans('messages.delete'), 'Reference deactivated: '.$reference->name, Auth::user(), $reference);
        return redirect()->route('references.index');
    }

    private function generateCollaborationCode()
    {
        do {
            $code = 'REF'.date('Ymd').random_int(1000, 9999);
        } while (ReferenceModel::where('collaboration_code', $code)->exists());

        return $code;
    }
}
