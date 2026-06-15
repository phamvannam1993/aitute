<?php

namespace App\Services;

use App\Models\UserSale;
use App\Repositories\FacebookRepository;
use App\Helper\Helpers;
use App\Models\AITuTeFile;
use App\Models\UserSaleContactStatus;
use Illuminate\Support\Facades\DB;

class ManagementService
{
    public function __construct(
        private FacebookRepository $facebookRepository,
    ) {
    }

    public function getUsersSale($data, $query = null)
    {
        // Default pagination limit and page
        $limit = $data->get('limit', 10);
        $page = $data->get('page', 1);  // Retrieve the page parameter, default is 1

        // Retrieve the search and created_at filters
        $created_at = $data->get('created_at', '');
        $search = $data->get('search', '');
        $filter = $data->get('filter', []);

        if (!$data->has('created_at')) {
            $created_at = null; // Default to today if no created_at is provided
        }

        // Initialize the date range (from, to) for created_at filter
        $from = $to = "";
        if (!empty($created_at)) {
            [$from, $to] = Helpers::convert_date_range_to_carbon($created_at);
        }

        // Get the authenticated user
        $user = auth('web')->user();

        // Build the query to fetch user sales
        $query = UserSale::where('sale_id', $user->id)
            ->with(['userSaleContactStatus:id,name', 'aiTuTeConversation.facebookConfig'])
            ->orderBy('id', 'desc')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('user_sale.name', 'like', '%' . $search . '%')
                        ->orWhere('user_sale.email', 'like', '%' . $search . '%')
                        ->orWhere('user_sale.age', 'like', '%' . $search . '%')
                        ->orWhere('user_sale.phone', 'like', '%' . $search . '%');
                });
            })
            ->when($from, function ($query) use ($to, $from) {
                return $query->whereBetween(DB::raw('DATE_FORMAT(user_sale.created_at,"%Y-%m-%d")'), [$from, $to]);
            });

        if (!empty($filter)) {
            $query->when(in_array('email', $filter), function ($query) {
                return $query->whereNotNull('user_sale.email')->where('user_sale.email', '!=', '');
            });

            $query->when(in_array('phone', $filter), function ($query) {
                return $query->whereNotNull('user_sale.phone')->where('user_sale.phone', '!=', '');
            });

            $query->when(in_array('appointment', $filter), function ($query) {
                return $query->whereNotNull('user_sale.appointment')->where('user_sale.appointment', '!=', '');
            });
        }

        // Apply pagination with the given limit and page
        $data = $query->paginate($limit, ['*'], 'page', $page);

        $data->getCollection()->transform(function ($user) {
            $user->conversation_status = $user->getConversationStatus();
            $user->page_id = data_get($user, 'aiTuTeConversation.facebookConfig.page_id');
            $user->page_name = data_get($user, 'aiTuTeConversation.facebookConfig.page_name');
            return $user;
        });

        $listUserSaleContactStatus = UserSaleContactStatus::all();

        // Return the paginated data as an array
        return [$data->toArray(), $listUserSaleContactStatus->toArray()];
    }

    public function getUsersSaleDetail($id)
    {
        $data = UserSale::where('id', $id)->first();
        $data['conversation_status'] = $data->getConversationStatus();
        $data['page_name'] = data_get($data, 'aiTuTeConversation.facebookConfig.page_name');
        $data['images'] = AITuTeFile::where('message_id', $data->message_id)->get();
        return $data;
    }
}
