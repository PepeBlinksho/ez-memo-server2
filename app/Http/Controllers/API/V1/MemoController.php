<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\ApiAuthException;
use App\Http\Controllers\Controller;
use App\Models\Memo;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class MemoController extends Controller
{
    /**
     * メモ参照API
     *
     * @param Request $request
     * @return void
     */
    public function view(Request $request)
    {
        $key = $request->get(key: 'key', default: null);

        if (!$key) {
            throw new ApiAuthException(message: 'no auth');
        }

        try {
            $uuid = Crypt::decryptString($key);
            $memo = Memo::findOrFail($uuid);
            return response()->json($memo);
        } catch (DecryptException $e) {
            return response()->json($e);
        }

        $uuid = Crypt::decryptString($key);
        $memo = Memo::findOrFail($uuid);
        return response()->json($memo);
    }

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

        $uuid = Str::uuid()->toString();

        $memo = new Memo();
        $memo->id = $uuid;
        $memo->user_id = $request->user() ? $request->uuid()->id : null;
        $memo->folder_id = $request->get(key: 'folder_id', default: null);
        $memo->title = $request->get(key: 'title');
        $memo->contents = $request->get(key: 'contents');
        $memo->is_public = $request->get(key: 'is_public', default: false);

        $memo->save();

        $encryptUUID = Crypt::encryptString($uuid);

        return response()->json([
            'key' => Crypt::encryptString($uuid),
            'url' => 'http://localhost/api/v1/memos?key='.$encryptUUID,
        ]);
    }
}
