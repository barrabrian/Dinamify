<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use HasFactory;
    use SoftDeletes;

    // protected $primaryKey = 'fid';

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'fid', $value)->withTrashed()->firstOrFail();
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'form_id', 'fid');
        // return $this->hasMany(Question::class);
    }

    public function deliverables()
    {
        return $this->hasMany(Deliverable::class, 'form_id', 'id');
    }

    public function responses()
    {
        return $this->hasMany(Deliverable::class, 'form_id', 'fid');
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
