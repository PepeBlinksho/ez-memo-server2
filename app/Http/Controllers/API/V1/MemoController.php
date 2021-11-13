<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\ApiAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\MemoRequest;
use App\Models\Memo;
use App\Protocols\MemoRepositoryProtocol;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class MemoController extends Controller
{
    /**
     * @var MemoRepositoryProtocol
     */
    protected $memoRepository;

    /**
     * MemoController constructor.
     * @param MemoRepositoryProtocol $memoRepository
     */
    public function __construct(MemoRepositoryProtocol $memoRepository)
    {
        $this->memoRepository = $memoRepository;
    }

    /**
     * メモ参照API
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiAuthException
     */
    public function view(Request $request)
    {
        // TODO: ログインユーザはIDで参照するかどうか別途で検討　2021/11/05 KGO
        $key = $request->get('key', null);

        if (!$key) {
            throw new ApiAuthException('no auth');
        }

        try {
            $uuid = Crypt::decryptString($key);
            $memo = Memo::findOrFail($uuid);
            return response()->json($memo);
        } catch (DecryptException $e) {
            throw new ApiAuthException('no auth');
        }
    }

    /**
     * メモ作成API
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(MemoRequest $request)
    {
        $memo = $this->memoRepository->create($request);

        dd($memo);

        return response()->json([
            'id' => $uuid,
            'key' => $encryptUUID,
            'url' => 'http://ez-memo.test/api/v1/memos?key='.$encryptUUID,
        ], 201);
    }

    /**
     * メモ更新API
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiAuthException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, string $id)
    {
        $key = $request->get('key', null);

        if (!$key and !$request->user()) {
            throw new ApiAuthException('no auth');
        }

        try {
            if (!$request->user()) {
                $uuid = Crypt::decryptString($key);
                if ($uuid !== $id) {
                    throw new ApiAuthException('no auth');
                }
            }

            $memo = Memo::findOrFail($id);

            if ($memo->user_id !== $request->user()->id) {
                throw new ApiAuthException('no auth', 403);
            }

            $this->validate($request, [
                'folder_id' => 'nullable|integer',
                'title' => 'required|string',
                'contents' => 'required',
                'is_public' => 'nullable|boolean',
            ]);

            $memo->folder_id = $request->get('folder_id', null);
            $memo->title = $request->get('title');
            $memo->contents = $request->get('contents');
            $memo->is_public = $request->get('is_public', false);

            $memo->update();

            return response()->json($memo, 202);
        } catch (DecryptException $e) {
            throw new ApiAuthException('no auth');
        }
    }

    /**
     * メモ削除API
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiAuthException
     */
    public function delete(Request $request, string $id)
    {
        $key = $request->get('key', null);

        if (!$key and !$request->user()) {
            throw new ApiAuthException('no auth');
        }

        try {
            if (!$request->user()) {
                $uuid = Crypt::decryptString($key);
                if ($uuid !== $id) {
                    throw new ApiAuthException('no auth');
                }
            }

            $memo = Memo::findOrFail($id);

            if ($memo->user_id !== $request->user()->id) {
                throw new ApiAuthException('no auth', 403);
            }

            $memo->delete();

            return response()->json(['status' => 'OK'], 204);
        } catch (DecryptException $e) {
            throw new ApiAuthException('no auth');
        }
    }
}
