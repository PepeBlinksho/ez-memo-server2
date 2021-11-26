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
     *
     * @return void
     */
    public function folders()
    {
        return response()->json(Folder::where('user_id', request()->id)
            ->where('parent_id', null)
            ->with(['children'])
            ->get());
    }

    /**
     * フォルダー作成API
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'parent_id' => 'integer',
        ]);

        $folder = new Folder();
        $folder->id = Str::uuid()->toString();
        $folder->name = $request->get(key: 'name');
        $folder->parent_id = $request->get(key: 'parent_id', default:null);
        $folder->user_id = $request->get(key: 'user_id', default:4);
        $folder->save();

        return response()->json(['status' => 'OK'], status:201);
    }

    /**
     * フォルダー更新API
     *
     * @param Request $request
     * @param Folder $folder
     * @return void
     */
    public function update(Request $request, Folder $folder)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'parent_id' => 'required|integer',
        ]);

        if ($folder->user_id !== $request->user()->id) {
            throw new ApiAuthException('no auth');
        }

        $folder->name = $request->get(key: 'name');
        $folder->parent_id = $request->get(key: 'parent_id', default:null);
        $folder->user_id = $request->get(key: 'user_id');
        $folder->update();

        return response()->json(['status' => 'OK'], status:201);
    }


    /**
     * フォルダー削除API
     *
     * @param Request $request
     * @param Folder $folder
     * @return void
     */
    public function delete(Request $request, Folder $folder)
    {
        if ($folder->user_id !== $request->user()->id) {
            throw new ApiAuthException('no auth');
        }

        dd($folder);

        $folder->delete();

        return response()->json(['status' => 'OK'], status:202);
    }
}
