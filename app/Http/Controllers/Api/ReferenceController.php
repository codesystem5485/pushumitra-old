<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController as BaseController;
use App\Models\ReferenceModel;
use App\Models\UserReference;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReferenceController extends BaseController
{
    public function getReferenceList(Request $request)
    {
        $query = ReferenceModel::query();

        if (!$request->boolean('all')) {
            $query->where('active', 1);
        }

        if ($request->filled('collaboration_code')) {
            $query->where('collaboration_code', $request->collaboration_code);
        }

        if ($request->filled('institution_code')) {
            $query->where('institution_code', $request->institution_code);
        }

        $response['results'] = $query->orderBy('name')->get();
        return $this->sendResponse($response, '', 200);
    }

    public function referenceDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:references,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError([], implode(',', $validator->errors()->all()), 400);
        }

        $response['result'] = ReferenceModel::find($request->id);
        return $this->sendResponse($response, '', 200);
    }

    public function addReference(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'institution_code' => 'nullable|max:255',
            'active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendError([], implode(',', $validator->errors()->all()), 400);
        }

        $reference = ReferenceModel::create([
            'name' => $request->name,
            'collaboration_code' => $this->generateCollaborationCode(),
            'institution_code' => $request->institution_code,
            'active' => $request->has('active') ? $request->active : 1,
            'create_by' => $request->user_id,
        ]);

        return $this->sendResponse(['result' => $reference], 'Reference created successfully', 200);
    }

    public function updateReference(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:references,id',
            'name' => 'required|max:255',
            'institution_code' => 'nullable|max:255',
            'active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendError([], implode(',', $validator->errors()->all()), 400);
        }

        $reference = ReferenceModel::find($request->id);
        $reference->name = $request->name;
        $reference->institution_code = $request->institution_code;
        if ($request->has('active')) {
            $reference->active = $request->active;
        }
        $reference->updated_by = $request->user_id;
        $reference->save();

        return $this->sendResponse(['result' => $reference], 'Reference updated successfully', 200);
    }

    public function deleteReference(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:references,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError([], implode(',', $validator->errors()->all()), 400);
        }

        $reference = ReferenceModel::find($request->id);
        $reference->active = 0;
        $reference->updated_by = $request->user_id;
        $reference->save();

        return $this->sendResponse(['result' => $reference], 'Reference deleted successfully', 200);
    }

    public function addUserReference(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required|exists:users,id',
            'referenceid' => 'required|exists:references,id',
            'refereddate' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError([], implode(',', $validator->errors()->all()), 400);
        }

        $userReference = UserReference::create([
            'userid' => $request->userid,
            'referenceid' => $request->referenceid,
            'refereddate' => $request->refereddate ?: Carbon::today()->toDateString(),
        ]);

        return $this->sendResponse(['result' => $userReference], 'User reference created successfully', 200);
    }

    public function getUserReferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError([], implode(',', $validator->errors()->all()), 400);
        }

        $response['results'] = UserReference::with('reference')->where('userid', $request->userid)->get();
        return $this->sendResponse($response, '', 200);
    }

    private function generateCollaborationCode()
    {
        do {
            $code = 'REF'.date('Ymd').random_int(1000, 9999);
        } while (ReferenceModel::where('collaboration_code', $code)->exists());

        return $code;
    }
}
