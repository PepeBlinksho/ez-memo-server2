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
    public function create(MemoRequest $request) : Memo
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

            return Memo::find($uuid);
        } catch (\Exception $exception) {
            //TODO: 例外処理　KGO　2021112
            dd($exception->getMessage());
        }
    }

    /**
     * Memoモデル更新
     * @param MemoRequest $request
     * @param Memo $memo
     * @return Memo
     */
    public function update(MemoRequest $request, Memo $memo): Memo
    {
        try {
            $memo->folder_id = $request->get('folder_id', null);
            $memo->title = $request->get('title');
            $memo->contents = $request->get('contents');
            $memo->is_public = $request->get('is_public', false);
            $memo->update();

            return Memo::find($memo->id);
        } catch (\Exception $exception) {
            //TODO: 例外処理　KGO　20211119
            dd($exception->getMessage());
        }
    }
}
