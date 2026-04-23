<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmissionValue extends Model
{
    use HasFactory;

    protected $table = 'form_submission_values';

    protected $fillable = [
        'form_submission_id',
        'form_field_id',
        'nilai',
    ];

    public function formSubmission()
    {
        return $this->belongsTo(FormSubmission::class, 'form_submission_id');
    }

    public function formField()
    {
        return $this->belongsTo(FormField::class, 'form_field_id');
    }
}