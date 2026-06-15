<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeyTransferHistory extends Model
{
    protected $table = 'key_transfer_history';

    protected $fillable = [
        'from_agent_id',
        'to_agent_id',
        'config_aff_id',
        'number_of_keys',
        'transferred_at',
        'note'
    ];

    protected $casts = [
        'transferred_at' => 'datetime'
    ];

    public function fromAgent()
    {
        return $this->belongsTo(Agent::class, 'from_agent_id');
    }

    public function toAgent()
    {
        return $this->belongsTo(Agent::class, 'to_agent_id');
    }

    public function package()
    {
        return $this->belongsTo(ConfigAff::class, 'config_aff_id');
    }
}
