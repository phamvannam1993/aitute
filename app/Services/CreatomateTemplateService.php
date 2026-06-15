<?php

namespace App\Services;

use App\Models\CreatomateTemplate;
use App\Repositories\CreatomateTemplateRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class CreatomateTemplateService
{
    protected $creatotemplateRepository;
    public function __construct(CreatomateTemplateRepository $creatotemplateRepository)
    {
        $this->creatotemplateRepository = $creatotemplateRepository;
    }

    public function getListTemplate() {
        try {
            $per_page = 8;
            $result = CreatomateTemplate::query()
                ->orderBy('created_at', 'asc')
                ->paginate($per_page);
            return $result;
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy danh sách template: ' . $e->getMessage());
            return null;
        }
    }

    public function search($search = '', $resolution='') {
        try {
            $per_page = 8;
            
            $query = CreatomateTemplate::query();
    
            if ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            }
    
            if ($resolution) {
                $query->where('resolution', $resolution);
            }
    
            $result = $query->orderBy('created_at', 'asc')->paginate($per_page);
    
            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy danh sách template: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi server'], 500);
        }
    }

    public function getTemplateById($id) {
        $record = $this->creatotemplateRepository->findOrFail($id);
        return $record;
    }
}
