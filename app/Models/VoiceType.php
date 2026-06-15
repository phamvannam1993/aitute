<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'demo',
        'user_id',
        'model',
    ];
    protected function demo(): Attribute
    {
    return Attribute::make(
        get: function (?string $value, array $attributes) {
            //check demo start with assets/audio
            $startWith = '/assets/audio';
            if ($attributes['model'] === 'cloned' && strpos($value, $startWith) !== 0) {
                return Helpers::preSignedS3Url($value);
            }
            return $value;
        }
    );
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
