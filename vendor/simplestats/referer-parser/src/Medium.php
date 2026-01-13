<?php

declare(strict_types=1);

namespace Snowplow\RefererParser;

enum Medium: string
{
    case SEARCH = 'search';
    case SOCIAL = 'social';
    case UNKNOWN = 'unknown';
    case INTERNAL = 'internal';
    case EMAIL = 'email';
    case INVALID = 'invalid';
}
