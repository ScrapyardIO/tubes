<?php

namespace ScrapyardIO\Tubes\Canvas;

use ScrapyardIO\Tubes\Contracts\Core\SupportsPartialRefresh;
use ScrapyardIO\Tubes\Contracts\Framebuffers\DeferredFramebuffer as DeferredFramebufferContract;
use ScrapyardIO\Tubes\Contracts\Framebuffers\DumpedBuffer;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\RenderType;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Framebuffer as FramebufferContract;
use ScrapyardIO\Tubes\Contracts\Framebuffers\ManagedFramebuffer as ManagedFramebufferContract;
use ScrapyardIO\Tubes\Contracts\Panels\PanelDevice;
use ScrapyardIO\Tubes\Panels\PanelException;
use ScrapyardIO\Tubes\Rendering\Renderer2D;

/**
 * IC panel canvas (sibling of {@see OSWindow}).
 *
 * Owns a chip {@see PanelDevice}, a pixel {@see FramebufferContract}, and a
 * {@see Renderer2D} bound to that buffer:
 *
 * - CPU path: Managed software FB (IC {@see FormatSpec}) + companion CPU renderer
 * - Engine path: renderer provisions headless Deferred (engine host FormatSpec);
 *   {@see present()} flushes/transcodes to the IC FormatSpec for transmit
 *
 * PanelIC is never a window canvas at construction time — but once handed out as
 * {@see Canvas}, consumers treat it interchangeably with {@see OSWindow}.
 */
abstract class PanelIC extends Canvas
{
    public function __construct(
        protected PanelDevice $device,
        protected FramebufferContract $framebuffer,
        protected Renderer2D $renderer,
    ) {
        self::assertPanelFramebuffer($this->framebuffer);
        $this->renderer->setFramebuffer($this->framebuffer);
    }

    /**
     * Engine Deferred buffers on a PanelIC must be headless (no window drawable).
     *
     * @throws PanelException
     */
    public static function assertPanelFramebuffer(FramebufferContract $framebuffer): void
    {
        if ($framebuffer instanceof DeferredFramebufferContract && ! $framebuffer->isHeadless()) {
            throw new PanelException(
                'PanelIC engine framebuffers must be headless (off-screen). '
                .$framebuffer::class.' is window-attached — use OSWindow for that surface, '
                .'or a headless Deferred sized for the panel.'
            );
        }
    }

    public function device(): PanelDevice
    {
        return $this->device;
    }

    public function renderer(): Renderer2D
    {
        return $this->renderer;
    }

    public function width(): int
    {
        return $this->device->width();
    }

    public function height(): int
    {
        return $this->device->height();
    }

    public function formatSpec(): FormatSpec
    {
        return $this->device->formatSpec();
    }

    public function framebuffer(): FramebufferContract
    {
        return $this->framebuffer;
    }

    /**
     * CPU-managed buffer when the canvas was built with one.
     *
     * @throws PanelException when an engine Deferred (or other non-Managed) buffer is bound
     */
    public function managedFramebuffer(): ManagedFramebufferContract
    {
        if (! $this->framebuffer instanceof ManagedFramebufferContract) {
            throw new PanelException(
                'PanelIC framebuffer is '.$this->framebuffer::class
                .' — managedFramebuffer() requires a ManagedFramebuffer.'
            );
        }

        return $this->framebuffer;
    }

    /**
     * True when the chip accepts PARTIAL dumps and the bound FB damage unit is
     * smaller than the whole surface (dirty rects / page runs).
     */
    public function supportsPartialRefresh(): bool
    {
        if (! $this->device instanceof SupportsPartialRefresh) {
            return false;
        }

        return ! $this->framebuffer->damageGranularity()->coversWholeSurface();
    }

    /**
     * Flush the bound buffer into the IC {@see FormatSpec} and transmit each dump.
     *
     * CPU Managed hosts use the IC FormatSpec (no present-time transcode).
     * Draw colours may be 0xRRGGBBAA — PixelStore packs into host depth on write.
     * Engine Deferred hosts still flush/transcode to the IC FormatSpec here.
     *
     * When {@see supportsPartialRefresh()} is true, DirtyRegions / PageSegment
     * PARTIAL frames are transmitted as-is (address / page window + data bytes).
     */
    public function present(): static
    {
        $frames = $this->framebuffer->flush($this->device->formatSpec(), as_array: true);

        if (! is_array($frames)) {
            $this->device->transmit(new DumpedBuffer(
                render_type: RenderType::FULL,
                metadata: $this->device->formatSpec(),
                raw_data: $frames,
                width: $this->width(),
                height: $this->height(),
            ));

            return $this;
        }

        foreach ($frames as $frame) {
            if ($frame instanceof DumpedBuffer) {
                $this->device->transmit($frame);
            }
        }

        return $this;
    }

    public function close(): static
    {
        $this->renderer->unsetFramebuffer();
        $this->device->close();

        return $this;
    }
}
