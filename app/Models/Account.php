<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }



    public function typeform_plugins()
    {
        return $this->hasMany(TypeformPlugin::class);
    }

    public function active_campaign_plugins()
    {
        return $this->hasMany(ActiveCampaignPlugin::class);
    }



    public function forms()
    {
        return $this->hasMany(Form::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function alternatives()
    {
        return $this->hasMany(Alternative::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function deliverables()
    {
        return $this->hasMany(Deliverable::class);
    }

    public function automations()
    {
        return $this->hasMany(Automation::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function ebooks()
    {
        return $this->hasMany(Ebook::class);
    }
}
