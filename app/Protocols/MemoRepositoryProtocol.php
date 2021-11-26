<?php


namespace App\Protocols;


use App\Http\Requests\MemoRequest;
use App\Models\Memo;

interface MemoRepositoryProtocol
{
    /**
     * Memoモデルの作成に関する定義
     * @param MemoRequest $request
     * @return Memo
     */
    public function create(MemoRequest $request) : Memo;

    /**
     * Memoモデルの更新に関する定義
     * @param MemoRequest $request
     * @param Memo $memo
     * @return Memo
     */
    public function update(MemoRequest $request, Memo $memo) : Memo;
}
