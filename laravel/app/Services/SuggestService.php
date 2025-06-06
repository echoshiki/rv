<?php

namespace App\Services;

use App\Models\Suggest;
use Illuminate\Support\Facades\Auth;

class SuggestService
{
    public function createSuggest(array $data)
    {
        $data['user_id'] = Auth::id();
        $data['content'] = nl2br($data['content']);
        return Suggest::create($data);
    }

    public function getUserSuggests()
    {
        return Suggest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getSuggestById(int $id)
    {
        return Suggest::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();
    }

    public function updateSuggest(Suggest $suggest, array $data)
    {        
        return $suggest->update($data);
    }

    public function updateSuggestById(int $id, array $data)
    {
        $suggest = Suggest::find($id);
        if (!$suggest) {
            return false;
        }
        return $this->updateSuggest($suggest, $data);
    }
    
    public function deleteSuggest(Suggest $suggest)
    {
        return $suggest->delete();
    }
}