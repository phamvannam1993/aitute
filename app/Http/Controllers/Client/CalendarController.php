<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\AjaxException;
use App\Exceptions\FacebookApiServiceException;
use App\Http\Controllers\Controller;
use App\Services\CalendarService;
use App\Traits\ApiResponses;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class CalendarController extends Controller
{
    use ApiResponses;
    private CalendarService $calendarService;

    public function __construct() {}
    public function index()
    {
        try {
            $this->calendarService = app(CalendarService::class);
            $params = $this->calendarService->getFacebooksFanpagesUser();
            return Inertia::render('Client/Calendar/Index', $params);
        } catch (FacebookApiServiceException $e) {
            if ($e->getMessage() == 'Không tìm thấy thông tin facebook') {
                return redirect()->route('social.facebook.create')->withErrors(['message' => 'Bạn cần cập nhật thông tin Facebook App trước khi sử dụng chức năng Quản Lý Social']);
            }
        }
    }

    public function getCalendar(Request $request)
    {
        try {
            $this->calendarService = app(CalendarService::class);
            $result = $this->calendarService->getCalendar($request->all());
            return response()->json($result);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }

    public function getFacebooksFanpagesUser(Request $request)
    {
        try {
            $this->calendarService = app(CalendarService::class);
            $params = $this->calendarService->getFacebooksFanpagesUser();
            return $this->okResponse($params['fanpages']);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }

    public function getSocialPostableType(Request $request)
    {
        try {
            $this->calendarService = app(CalendarService::class);
            $result = $this->calendarService->getSocialPostableType($request->all());
            return $this->okResponse($result);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }    
}
