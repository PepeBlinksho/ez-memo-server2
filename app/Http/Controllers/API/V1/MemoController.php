<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class MemoController extends Controller
{
    /**
     * メモ作成API
     *
     * @param Request $request
     * @return void
     */

    public function store(Request $request)
    {
        $this->validate($request, [
            'folder_id' => 'nullable|integer',
            'title' => 'required|string',
            'contents' => 'required',
            'is_public' => 'nullable|boolean',
        ]);

        $memo = new Memo();
        $memo->id = Str::uuid();
        $memo->user_id = $request->user() ? $request->uuid()->id : null;
        $memo->folder_id = $request->get( key: 'folder_id', default:null);
        $memo->title = $request->get( key: 'title');
        $memo->contents = $request->get( key: 'contents');
        $memo->is_public = $request->get( key: 'is_public', default:false);

        $memo->save();

        return response()->json([
            'key' => Crypt::encryptString($memo->id),
        ]);
    }
}
