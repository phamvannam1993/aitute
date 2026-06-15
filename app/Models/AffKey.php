<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AffKey extends Model
{
    use HasFactory;

    protected $table = 'aff_keys';

    protected $fillable = [
        'config_aff_id',
        'key',
        'is_used',
        'user_id',
        'agent_id'
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function configAff()
    {
        return $this->belongsTo(ConfigAff::class, 'config_aff_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

}
