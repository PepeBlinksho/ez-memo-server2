<?php

namespace App\Repositories;

use App\Http\Requests\MemoRequest;
use App\Models\Memo;
use App\Protocols\MemoRepositoryProtocol;
use Illuminate\Support\Str;

class MemoRepository implements MemoRepositoryProtocol
{
    /**
     * Memoモデル作成
     * @param MemoRequest $request
     * @return Memo
     */
    public function create(MemoRequest $request): Memo
    {
        try {
            $uuid = Str::uuid()->toString();
            $memo = new Memo();
            $memo->id = $uuid;
            $memo->user_id = $request->user() ? $request->user()->id : null;
            $memo->folder_id = $request->get('folder_id', null);
            $memo->title = $request->get('title');
            $memo->contents = $request->get('contents');
            $memo->is_public = $request->get('is_public', false);
            $memo->save();

            $memo = Memo::find($uuid);

            return $memo;
        } catch (\Exception $exception) {
            //TODO: 例外処理　KGO　2021112
            dd($exception->getMessage());
        }
    }
}
