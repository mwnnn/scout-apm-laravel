<?php

declare(strict_types=1);

namespace Scoutapm\Laravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Scoutapm\ScoutApmAgent;

final class IgnoredEndpoints
{
    /** @var ScoutApmAgent */
    private $agent;

    const DEFAULT_SAMPLING_PERCENTAGE = 4;

    public function __construct(ScoutApmAgent $agent)
    {
        $this->agent = $agent;
    }

    /** @return mixed */
    public function handle(Request $request, Closure $next)
    {
        // Check if the request path we're handling is configured to be
        // ignored, and if so, mark it as such.
        // also do sampling with certain percentage
        if ($this->agent->ignored('/' . $request->path())) {
            $this->agent->ignore();
        } else if (rand(0, 100) >= (int) env('SCOUT_APM_SAMPLING_PERCENTAGE', self::DEFAULT_SAMPLING_PERCENTAGE)) {
            $this->agent->ignore();
        }

        return $next($request);
    }
}
