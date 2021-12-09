<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\ApiAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\MemoRequest;
use App\Http\Resources\V1\MemoResource;
use App\Models\Memo;
use App\Protocols\MemoRepositoryProtocol;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

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
     * メモリストAPI
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function memos()
    {
        return MemoResource::collection(Memo::where('is_archive', false)
            // ->where('is_public', true)
            ->orWhere(function ($query) {
                $query->where('is_archive', false);
                $query->where('user_id', request()->user()->id ?? 0);
            })
            ->filter(request('keyword',  null))
            ->orderBy('user_id', 'desc')
            ->latest()
            ->simplePaginate(30));
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
            return (new MemoResource(Memo::findOrFail(Crypt::decryptString($key))))
                ->response()
                ->setStatusCode(200);
        } catch (DecryptException $e) {
            throw new ApiAuthException('no auth');
        }
    }

    /**
     * メモ作成API
     * @param MemoRequest $request
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function store(MemoRequest $request)
    {
        return (new MemoResource($this->memoRepository->create($request)))->response()->setStatusCode(201);
    }

    /**
     * メモ更新API
     * @param MemoRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse|object
     * @throws ApiAuthException
     */
    public function update(MemoRequest $request, string $id)
    {
        $request->checkAuthKey();
        $memo = Memo::findOrFail($id);
        $request->authorizeUser($memo);

        return (new MemoResource($this->memoRepository->update($request, $memo)))
            ->response()
            ->setStatusCode(202);
    }

    /**
     * メモ削除API
     * @param MemoRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiAuthException
     */
    public function delete(MemoRequest $request, string $id)
    {
        $request->checkAuthKey();
        $memo = Memo::findOrFail($id);
        $request->authorizeUser($memo);
        $memo->delete();

        return response()->json(['status' => 'OK'], 204);
    }
}
