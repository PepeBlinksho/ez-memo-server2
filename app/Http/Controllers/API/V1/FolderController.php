<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\ApiAuthException;
use App\Http\Controllers\Controller;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FolderController extends Controller
{
    /**
     * フォルダー一覧API
     * @return \Illuminate\Http\JsonResponse
     */
    public function folders()
    {
        return response()->json(Folder::where('user_id', request()->user()->id)
            ->where('parent_id', null)
            ->with(['children'])
            ->get());
    }

    /**
     * フォルダー作成API
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'parent_id' => 'nullable|string',
        ]);

        $folder = new Folder();
        $folder->id = Str::uuid()->toString();
        $folder->name = $request->get('name');
        $folder->parent_id = $request->get('parent_id', null);
        $folder->user_id = $request->user()->id;
        $folder->save();

        return response()->json(['status' => 'OK'], 201);
    }

    /**
     * フォルダー更新API
     * @param Request $request
     * @param Folder $folder
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiAuthException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Folder $folder)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'parent_id' => 'nullable|string',
        ]);

        if ($folder->user_id !== $request->user()->id) {
            throw new ApiAuthException('no auth');
        }

        $folder->name = $request->get('name');
        $folder->parent_id = $request->get('parent_id', null);
        $folder->update();

        return response()->json(['status' => 'OK'], 202);
    }

    /**
     * フォルダー削除API
     * @param Request $request
     * @param Folder $folder
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiAuthException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Request $request, Folder $folder)
    {
        if ($folder->user_id !== $request->user()->id) {
            throw new ApiAuthException('no auth');
        }

        $folder->delete();

        return response()->json(['status' => 'OK'], 204);
    }
}
