<?php

namespace Cels\Utilities\CSP\Constants;

enum Value: string
{
    case None = 'none';
    case Self = 'self';
    case StrictDynamic = 'strict-dynamic';
    case ReportSample = 'report-sample';
    case InlineSpeculationRules = 'inline-speculation-rules';

    // Unsafe values
    case UnsafeInline = 'unsafe-inline';
    case UnsafeEval = 'unsafe-eval';
    case UnsafeHashes = 'unsafe-hashes';
    case WasmUnsafeEval = 'wasm-unsafe-eval';
}