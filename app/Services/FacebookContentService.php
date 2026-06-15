<?php

namespace App\Services;

use App\Models\FacebookContent;
use App\Helper\Helpers;
use App\Models\User;
use App\Models\CreditHistory;
use App\Services\FacebookApiService;
use App\Repositories\UserRepository;
use App\Repositories\FacebookFanpageRepository;
use Carbon\Carbon;

class FacebookContentService
{
    private $postFacebookService;
    private FacebookApiService $facebookApiService;

    public function __construct(
        private FacebookFanpageRepository $facebookFanpageRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function apiSaveContent($param) {
        if(isset($param->post["title"]) && $param->user_id  && $param->project_id && isset($param->post["content"])) {
            $title = $param->post["title"];
            $content = $param->post["content"];
            $maxPost = (int) $param->maxPost;
            $goal = isset($param->post["goal"]) ? $param->post["goal"] : '';
            $user = User::where("id", $param->user_id)->first();
            if(!$user) {
                return response()->json(["success" => false, "message" => "Không tồn tại user"]);
            }
            $total = FacebookContent::where("user_id", $param->user_id)->where("project_id", $param->project_id)->count();
            if($total >= $maxPost) {
                return response()->json(["success" => false, "message" => "Đã đủ $maxPost bài"]);
            }
            $dataSave = [
                "user_id" => $param->user_id,
                "project_id" => $param->project_id,
                "goal" =>  $goal,
                "title" => $title,
                "content" => $content
            ];
            $check = $this->calCredit($content, $user->id);
            if(!$check) {
                return response()->json(["success" => false, "message" => "Không đủ tiền để thực hiện"]);
            }
            $result = FacebookContent::insert($dataSave);
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "message" => "Không đủ dữ liệu"]);
        }
    }

    public function deleteFacebookContent()
    {
        return FacebookContent::where('user_id', request()->user()->id)->where("project_id", request('project_id'))->delete();
    }

    public function getList($userId, $project_id, $per_page = 10) {
        try {
            return FacebookContent::where('user_id', '=', $userId)->where("project_id", $project_id)->select('id', 'title', 'content', 'goal', 'status')
                ->orderBy('goal', 'ASC')
                ->paginate($per_page);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getTotal($userId, $project_id) {
        return FacebookContent::where("user_id", $userId)->where("project_id", $project_id)->count();
    }

    public function getOne($id, $isNext = false) {
        $item = FacebookContent::where("id", $id)->first();

        if($isNext && $item) {
            $itemNext =  FacebookContent::where("id", '>', $id)->where("project_id", $item->project_id)->first();
            if(!$itemNext) {
                $item = FacebookContent::where("id", '<', $id)->where("project_id", $item->project_id)->first();
                $item->is_next = false;
            } else {
                $item = $itemNext;
                $item->is_next = true;
            }
        }
        return $item;
    }

    public function delete($id, $userId) {
        return FacebookContent::where("id", $id)->where("user_id", $userId)->delete();
    }

    public function update($param) {
        $id = $param->id;
        if(!$id) {
            return response()->json(["success" => false, "message" => "Không đủ dữ liệu"]);
        }
        $dataUpdate = [];
        if($param->images) {
            $dataUpdate["images"] = $param->images;
            $dataUpdate["is_video"] = false;
        }
        if($param->content) {
            $dataUpdate["content"] = $param->content;
        }
        if($param->video_url) {
            $dataUpdate["video_url"] = $param->video_url;
        }
        if($param->status) {
            $dataUpdate["status"] = $param->status;
        }
        if($param->videos) {
            $dataUpdate["videos"] = $param->videos;
        }
        if($param->is_video && $param->is_video == 2) {
            $images = FacebookContent::find($id)->images;
            $images = json_decode($images, true);
            for($i = 0; $i < count($images); $i++) {
                $images[$i]["is_post"] = false;
            }
            $dataUpdate["images"] = $images;
            $dataUpdate["is_video"] = true;
        }
        if($param->is_video && $param->is_video == 1) {
            $dataUpdate["is_video"] = false;
        }
        if($param->remove_video) {
            $dataUpdate["is_video"] = false;
            $dataUpdate["video_url"] = "";
        }
        if(!$param->published) {
            if($param->post_date) {
                $date = Carbon::createFromFormat('D M d Y H:i:s e', substr($param->post_date, 0, 31))->toDateString();
                if($param->post_time) {
                    $postTime = json_decode($param->post_time, true);
                    $date = $date.' '.$postTime["hours"]. ':'.$postTime["minutes"].':'.$postTime["seconds"];
                }
                $dataUpdate["post_date"] = $date;
            }
            $dataUpdate["is_post"] = true;
        } else {
            $dataUpdate["post_date"] = null;
            $dataUpdate["is_post"] = false;
        }
        if($param->social_postable_id) {
            $dataUpdate["facebook_fanpage_id"] = $param->social_postable_id;
        }
        if(empty($dataUpdate)) {
            return response()->json(["success" => false, "message" => "Không đủ dữ liệu"]);
        }
        FacebookContent::where("id", $id)->update($dataUpdate);
        return response()->json(["success" => true, "message" => "Cập nhật thành công"]);
    }

    public function confirmPostFacebook($userId, $param) {
        $listFacebookContents = FacebookContent::where("status", 3)->where("project_id",$param->project_id)->where("user_id", $userId)->get();
        $post_date = null;
        $time_between =  (int)$param->time_between;
        if($param->post_date) {
            $post_date = Carbon::createFromFormat('D M d Y H:i:s e', substr($param->post_date, 0, 31))->toDateString();
            if($param->post_time) {
                $postTime = json_decode($param->post_time, true);
                $postTime["hours"]  = $postTime["hours"] > 10 ? $postTime["hours"] : '0'.$postTime["hours"];
                $postTime["minutes"]  = $postTime["minutes"] > 10 ? $postTime["minutes"] : '0'.$postTime["minutes"];
                $postTime["seconds"]  = $postTime["seconds"] > 10 ? $postTime["seconds"] : '0'.$postTime["seconds"];
                $post_date = $post_date.' '.$postTime["hours"]. ':'.$postTime["minutes"].':'.$postTime["seconds"];
            }
            $dateTime = date('Y-m-d H:i:s');
            if($post_date < $dateTime) {
                return response()->json(["success" => false, "message" => "Thời gian đăng bài không được nhỏ hơn thời gian hiện tại"]);
            }
        } else {
            return response()->json(["success" => false, "message" => "Thời gian đăng bài không được nhỏ hơn thời gian hiện tại"]);
        }

        foreach($listFacebookContents as $key => $fbContent) {
            $fbContent->update(["is_post" => true, "post_date" => $post_date]);
            $post_date = new \DateTime($post_date);
            if($time_between == 30) {
                $post_date->modify('+30 minutes');
            } else {
                $post_date->modify('+'.$time_between.' hours');
            }
            $post_date = $post_date->format('Y-m-d H:i:s');
        }
        return response()->json(["success" => true]);
    }

    public function postFacebookCron() {
        $datetime = new \DateTime();
        $datetime->modify('+40 minutes'); // Thêm 40 phút
        $listFacebookContents = FacebookContent::where("status", 3)->where("post_date", '<=', $datetime)->where("is_post", true)->get();
        foreach($listFacebookContents as $facebookContent) {
            $this->postFacebookByID($facebookContent->id, true);
        }
    }

    public function postFacebookByID($id, $isCron = false)
    {
        try {
            $fbContent = FacebookContent::where("id", $id)->first();
            if(!$fbContent->is_post && $isCron) {
                return true;
            }
            $facebook_fanpage_id = $fbContent->facebook_fanpage_id;
            $files = [];
            if($fbContent->is_video) {
                $files[] = [
                    "url" => $fbContent->video_url,
                    "type" => "video"
                ];
            }
            if(!$fbContent->is_video) {
                $images = json_decode($fbContent->images, true);
                if($images) {
                    foreach($images as $image) {
                        if(isset($image["is_post"]) && $image["is_post"] && isset($image["imageRef"]["s3_url"])) {
                            $files[] = [
                                "url" =>  $image["imageRef"]["s3_url"],
                                "type" => "image"
                            ];
                        }
                    }
                }
            }

            $content = $fbContent->content;
            $params = [
                "content" => $content,
                "social_postable_type" => "FacebookFanpage",
                "published" => 1,
                "files" => $files
            ];

            if($isCron) {
                $dateTime = date('Y-m-d H:i:s');
                if($fbContent->post_date > $dateTime) {
                    $params["published"] = "0";
                    $postDateTime = new \DateTime($fbContent->post_date);
                    $params["scheduled_publish_time"] = $postDateTime->format('Y/m/d H:i');
                }
            }
            FacebookContent::where("id", $id)->update(["is_post" => false]);
            $this->facebookApiService = app(FacebookApiService::class);
            $userId = $fbContent->user_id;

            $facebookFanpage = $this->facebookFanpageRepository->getFacebookFanpageOfUser($facebook_fanpage_id, $userId);
            if (!$facebookFanpage) {

            }

            if (auth('admin')->check()) {
                $user = auth('web')->user();
            } else {
                $user =  $this->userRepository->find($userId);
            }
            $result = $this->facebookApiService->setConfig($user)
                ->postToFanpage(
                    $params,
                    $facebookFanpage->page_id,
                    $facebookFanpage->page_access_token,
                    $files ?? []
                );
            return $result;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function calCredit($content, $userId) {
        $tokenCounter = new TokenCounter();
        $model_code = 'claude-3-5-sonnet-20240620';
        $input_tokens = $tokenCounter->countTokens($content, $model_code);
        $output_tokens = $tokenCounter->estimateOutputTokens($input_tokens, $model_code, 'translation');
        $total_price = Helpers::calculateCredit('claude-3-5-sonnet-20240620', $input_tokens, $output_tokens, 0, 0);
        $user = User::where("id", $userId)->first();
        if($user->credit > 0) {
            $credit = $user->credit - $total_price;
            if($credit < 0) {
                $credit = 0;
            }
            User::where("id", $userId)->update(["credit" => $credit]);

            CreditHistory::insert([
                "user_id" => $user->id,
                "screen_id" => 37,
                "credit" => $total_price,
                'feature_id' => 39,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return true;
        } else {
            return false;
        }
    }
}
