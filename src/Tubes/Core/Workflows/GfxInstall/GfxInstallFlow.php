<?php

namespace ScrapyardIO\Tubes\Core\Workflows\GfxInstall;

use Fabricate\Sketches\Flow\Flow;
use Fabricate\Sketches\Flow\Node;

/**
 * Detect → prompt → PHP extension → binding wrapper → gfx require → publish config.
 */
class GfxInstallFlow extends Flow
{
    public static function make(): self
    {
        $detect = new DetectHostNode;
        $prompt = new PromptGfxTargetsNode;
        $extension = new EnsurePhpExtensionNode;
        $wrapper = new EnsureExtensionWrapperNode;
        $composer = new ComposerRequireGfxNode;
        $publish = new PublishFramebufferConfigNode;
        $empty = new class extends Node
        {
            public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
            {
                $shared['cancelled'] = true;

                return 'default';
            }
        };
        $fail = new class extends Node
        {
            public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
            {
                $shared['failed'] = true;

                return 'default';
            }
        };

        $detect->next($prompt);
        $prompt->next($extension);
        $prompt->on('empty')->next($empty);
        $prompt->on('fail')->next($fail);
        $extension->next($wrapper);
        $extension->on('fail')->next($fail);
        $wrapper->next($composer);
        $wrapper->on('fail')->next($fail);
        $composer->next($publish);
        $composer->on('fail')->next($fail);
        $publish->on('fail')->next($fail);

        return new self($detect);
    }
}
