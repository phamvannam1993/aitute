<?php

namespace App\Services;

use App\Helper\Helpers;
use App\Repositories\AIBusinessProjectRepository;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class DifyService
{
    protected $appKey;
    protected $appKeyV2;
    protected $baseUrl;

    protected $selfHostAppKey;
    protected $selfHostAppKeyV2;
    protected $selfHostBaseUrl;

    public function __construct(private readonly AIBusinessProjectRepository $aiBusinessProjectRepository)
    {
        $this->appKey = config('services.dify_chat.api_key');
        $this->appKeyV2 = config('services.dify_chat.api_key_v2');
        $this->baseUrl = config('services.dify_chat.base_url');

        $this->selfHostAppKey = config('services.self_host_dify.api_key');
        $this->selfHostAppKeyV2 = config('services.self_host_dify.api_key_v2');
        $this->selfHostBaseUrl = config('services.self_host_dify.base_url');
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getAppKey($isExpert = false)
    {
        return $isExpert ? $this->appKeyV2 : $this->appKey;
    }

    public function setAppKey($appKey)
    {
        if (!empty($appKey)) {
            $this->appKey = $appKey;
        }
        return $this;
    }

    public function getSelfHostBaseUrl()
    {
        return $this->selfHostBaseUrl;
    }

    public function getSelfHostAppKey($isExpert = false)
    {
        return $isExpert ? $this->selfHostAppKeyV2 : $this->selfHostAppKey;
    }

    private function removeUnuseParams(&$options)
    {
        unset($options['is_backup']);
        unset($options['is_expert']);
        unset($options['content_type']);
    }

    private function __initClient($options)
    {
        $headers = [];
        $headers['Content-Type'] = $options['content_type'] ?? 'application/json';
        if (isset($options['app_key'])) {
            $headers['Authorization'] = 'Bearer ' . $options['appKey'];
        } else {
            $headers['Authorization'] = 'Bearer '. ($options['is_backup'] ? $this->getSelfHostAppKey($options['is_expert'] ?? false) : $this->getAppKey($options['is_expert'] ?? false));
        }
        $options['headers'] = array_merge($headers, $options['headers'] ?? []);
        $this->removeUnuseParams($options);
        $client = new \GuzzleHttp\Client( $options);
        return $client;
    }

    public function getDataSenChat($data, $project)
    {
        $dataArray = json_decode($data, true);
        $currentStep = $dataArray['currentStep'] ?? '';
        $metaData = $project->meta_data ? json_decode($project->meta_data, true) : null;
        $dataArray['the_loai'] = $metaData['the_loai'] ?? null;
        $dataJson = $project->data_json;
        if ($currentStep !== 'buoc5') {
            $dataArray['answer'] = $dataJson ? ($dataJson['information_project']['answer'] ?? null) : null;
        }
        return json_encode($dataArray, JSON_UNESCAPED_UNICODE);
    }
    public function sendChat($options, $retry = false)
    {
        try {
            Helpers::logInfo('Dify Chat message payload', $options);
            $options['is_backup'] = false;
            $client = $this->__initClient($options);
            $chatUrl = $this->getBaseUrl() . '/v1/chat-messages';
            $data = $client->post($chatUrl, $options);
        } catch (ClientException $ce) {
            //@todo tính nắng call backup self host test lại và làm lại sau
            Log::error($ce->getMessage());
            if ($retry) {
                $this->sendChat($options);
            }
            //$project = $this->aiBusinessProjectRepository->find($options['json']['project_id']);
            //if ($project) {
            //    $options['json']['conversation_id'] = null;
            //    $options['json']['query'] = $this->getDataSenChat($options['json']['query'], $project);
            //}
            //$data = $this->sendChatSelfHost($options);
            throw $ce;
        } catch (\Exception $e) {
            Helpers::logException($e);
            if ($retry) {
                $this->sendChat($options);
            }
            throw $e;
        }
        return $data;
    }

    public function sendChatSelfHost($options)
    {
        try {
            $options['is_backup'] = true;
            $client = $this->__initClient($options);
            $chatUrl = $this->getSelfHostBaseUrl() . '/v1/chat-messages';
            $data['meta_data'] = [
                'is_dify_backup' => true
            ];
            $data = $client->post($chatUrl, $options);
        } catch (ClientException $ce){
            Log::error($ce->getMessage());
            throw $ce;
        } catch (\Exception $e) {
            Helpers::logException($e);
            throw $e;
        }

        return $data;
    }
}