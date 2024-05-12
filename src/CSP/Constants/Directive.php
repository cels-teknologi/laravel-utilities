<?php

namespace Cels\Utilities\CSP\Constants;

enum Directive: string
{
    // Fetch directives
    case ChildSrc = 'child-src';
    case ConnectSrc = 'connect-src';
    case DefaultSrc = 'default-src';
    /**
     * Experimental, might change in the future.
     * 
     * @see  https://caniuse.com/mdn-http_headers_content-security-policy_fenced-frame-src
     * @see  https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/fenced-frame-src
     */
    case FencedFrameSrc = 'fenced-frame-src';
    case FontSrc = 'font-src';
    case FrameSrc = 'frame-src';
    case ImgSrc = 'img-src';
    case ManifestSrc = 'manifest-src';
    case MediaSrc = 'media-src';
    case ObjectSrc = 'object-src';
    /**
     * @deprecated
     * @see  https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/prefetch-src
     */
    case PrefetchSrc = 'prefetch-src';
    case ScriptSrc = 'script-src';
    case ScriptSrcAttr = 'script-src-attr';
    case ScriptSrcElem = 'script-src-elem';
    case StyleSrc = 'style-src';
    case StyleSrcAttr = 'style-src-attr';
    case StyleSrcElem = 'style-src-elem';
    case WorkerSrc = 'worker-src';
    
    // Document directives
    case BaseUri = 'base-uri';
    case Sandbox = 'sandbox';

    // Navigation directives
    case FormAction = 'form-action';
    case FrameAncestors = 'frame-ancestors';

    // Reporting directives
    /**
     * @deprecated  Use `CSPDirective::ReportTo` instead
     * @see  https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/report-uri
     */
    case ReportUri = 'report-uri';
    case ReportTo = 'report-to';

    // Other directives
    /**
     * Experimental, might change in the future.
     * 
     * @see  https://caniuse.com/mdn-http_headers_content-security-policy_fenced-frame-src
     * @see  https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/fenced-frame-src
     */
    case RequireTrustedTypesFor = 'require-trusted-types-for';
    /**
     * Experimental, might change in the future.
     * 
     * @see  https://caniuse.com/mdn-http_headers_content-security-policy_fenced-frame-src
     * @see  https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/fenced-frame-src
     */
    case TrustedTypes = 'trusted-types';
    case UpgradeInsecureRequests = 'upgrade-insecure-requests';

    // Deprecated directives
    /**
     * @deprecated
     * @see  https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/block-all-mixed-content
     */
    case BlockAllMixedContent = 'block-all-mixed-content';
    /**
     * @deprecated
     * @see  https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/plugin-types
     */
    case PluginTypes = 'plugin-types';
    /**
     * @deprecated
     * @see  https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/referrer
     */
    case Referrer = 'Referrer';
}