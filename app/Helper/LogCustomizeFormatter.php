<?php

namespace App\Helper;

use Illuminate\Http\Request;
use Monolog\Formatter\LineFormatter;

class LogCustomizeFormatter
{
    const LOG_FORMAT = "[%datetime%] %channel%.%level_name%, 'message': %message%, 'context': %context%\n'extra': %extra%}\n";
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var Request
     */
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function __invoke($logger)
    {
        $formatter = new LineFormatter(self::LOG_FORMAT, self::DATE_FORMAT);

        /** @var \Monolog\Logger $logger */
        foreach ($logger->getHandlers() as $handler) {
            if (!($handler instanceof \Monolog\Handler\NullHandler)) {
                $handler->setFormatter($formatter);
                $this->customizeHandler($handler);
            }
        }
    }

    /**
     * configure display data to %field%
     * %field% in LOG_FORMAT
     *
     * @param $handler
     */
    public function customizeHandler($handler)
    {
        $handler->pushProcessor(function ($record) {

            $extra['url'] = $this->request->url();
            $extra['method'] = $this->request->getMethod();
            $extra['request-params'] = $this->request->all();
            $extra['request-route-params'] = empty($this->request->route()) ? '' : $this->request->route()->parameters();
            $extra['ip'] = $this->request->getClientIp();
            $extra['user-agent'] = $this->request->header()['user-agent'] ?? '';
            $extra['user_id'] = auth('web')?->user()?->id ?? null;
            $extra['admin_id'] = auth('admin')?->user()?->id ?? null;
            $record['extra'] = $extra;

            return $record;
        });
    }
}
