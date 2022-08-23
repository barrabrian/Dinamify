<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'qid', $value)->withTrashed()->firstOrFail();
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id', 'fid');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function alternatives()
    {
        return $this->hasMany(Alternative::class, 'question_id', 'qid');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'qid');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
