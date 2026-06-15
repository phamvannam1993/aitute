<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiAssistantCriteria extends Model
{
    use HasFactory;
    protected $table = 'ai_assistant_criteria';

    protected $fillable = [
        'ai_assistant_id',
        'name',
        'type',
        'value',
        'label',
        'multiple',
        'selectedValue',
        'placeholder'
    ];

    public function aiAssistant()
    {
        return $this->belongsTo(AiAssistant::class);
    }

    public function getValueAttribute($value)
    {
        // Nếu 'type' là 'select' và 'value' là chuỗi JSON, giải mã thành mảng
        if ($this->type === 'select' && is_string($value)) {
            return json_decode($value, true) ?? []; // Trả về mảng rỗng nếu không thể giải mã
        }

        return $value; // Trả về giá trị gốc nếu không phải là chuỗi JSON
    }

    public function getMultipleAttribute($value)
    {
        return $value == 1;
    }
}
