<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Automation extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function deliverable()
    {
        return $this->belongsTo(Deliverable::class);
    }

    public function active_campaign_plugin()
    {
        return $this->belongsTo(ActiveCampaignPlugin::class, 'active_campaign_id', 'id');
    }

    public function typeform_plugin()
    {
        return $this->belongsTo(TypeformPlugin::class, 'typeform_id', 'id');
    }

    public function email_question()
    {
        return $this->belongsTo(Question::class, 'email_question_id', 'id');
    }


    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
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
